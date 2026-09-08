<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\ContentPage;
use App\Models\HazardLearningProgress;
use App\Models\Language;
use App\Models\MockTest;
use App\Models\MockTestHistory;
use App\Models\Question;
use App\Models\SubSection;
use App\Models\Topic;
use Illuminate\Http\Request;

class TheoryTestController extends Controller
{
    public function practice(Topic $topic)
    {
        $questions = $topic->questions()->with('choices')->get();
        $questions->each(fn ($question) => $question->setRelation('choices', $question->choices->shuffle()->values()));
        $contentPages = $topic->contentPages()->with('clips')->get();

        $practiceItems = $questions
            ->map(fn ($question) => array_merge($question->toArray(), [
                'item_type' => 'question',
            ]))
            ->concat($contentPages->map(fn ($contentPage) => array_merge($contentPage->toArray(), [
                'item_type' => $contentPage->type,
            ])))
            ->shuffle()
            ->values();

        // The browser selects one of these category-eligible ads after reading the
        // learner's locally stored language preference.
        $preferredLanguage = auth('web')->user()?->preferredLanguage ?: Language::query()->where('code', 'en')->first();
        $ads = Ad::currentlyRunning()
            ->where('display_type', 'question')
            ->forLanguage($preferredLanguage?->id)
            ->where(function ($query) use ($topic) {
                $query->where('targets_all_topics', true)
                    ->orWhere(fn ($legacyGlobal) => $legacyGlobal->where('targets_all_categories', true)->whereDoesntHave('topics'))
                    ->orWhereHas('topics', fn ($topicQuery) => $topicQuery->where('topics.id', $topic->id));

                // Compatibility for category-targeted advertisements created before topic targeting.
                if ($topic->topicable_type === Category::class) {
                    $query->orWhere(fn ($legacy) => $legacy
                        ->where('targets_all_topics', false)
                        ->whereHas('categories', fn ($categories) => $categories->where('categories.id', $topic->topicable_id)));
                }
            })
            ->with('languages:id,name,code')
            ->inRandomOrder()
            ->get();

        $languages = Language::active()->get();

        return view('theory.practice', compact('topic', 'practiceItems', 'ads', 'languages', 'preferredLanguage'));
    }

    public function result()
    {
        return view('theory.result');
    }

    public function hazardLibrary(Topic $topic)
    {
        $pages = $topic->contentPages()->where('type', 'cgi_clips')->with('clips')->latest()->get();
        $latestPages = $pages->take(4);
        $categoryGroups = $pages
            ->sortBy(fn ($page) => mb_strtolower(($page->library_category ?: 'Other hazards').'|'.($page->admin_title ?: '')))
            ->groupBy(fn ($page) => $page->library_category ?: 'Other hazards');
        $watchedPageIds = auth('web')->check()
            ? HazardLearningProgress::query()
                ->where('user_id', auth('web')->id())
                ->whereIn('content_page_id', $pages->pluck('id'))
                ->pluck('content_page_id')
                ->map(fn ($id) => (int) $id)
                ->values()
            : collect();

        return view('theory.hazard_library', compact('topic', 'pages', 'latestPages', 'categoryGroups', 'watchedPageIds'));
    }

    public function hazardStudy(ContentPage $contentPage)
    {
        abort_unless($contentPage->type === ContentPage::TYPE_CGI_CLIPS, 404);

        $contentPage->load('clips');
        $topic = $contentPage->topic;
        $practiceItems = collect([array_merge($contentPage->toArray(), [
            'item_type' => ContentPage::TYPE_CGI_CLIPS,
        ])]);
        $ads = collect();
        $languages = Language::active()->get();
        $preferredLanguage = auth('web')->user()?->preferredLanguage ?: Language::query()->where('code', 'en')->first();
        $backUrlOverride = route('theory.hazard_library', $topic);
        $hazardStudyPage = $contentPage;
        $hazardProgressUrl = auth('web')->check() ? route('theory.hazard_watched', $contentPage) : null;

        return view('theory.practice', compact(
            'topic', 'practiceItems', 'ads', 'languages', 'preferredLanguage', 'backUrlOverride', 'hazardStudyPage', 'hazardProgressUrl'
        ));
    }

    public function markHazardWatched(ContentPage $contentPage)
    {
        abort_unless($contentPage->type === ContentPage::TYPE_CGI_CLIPS, 404);

        HazardLearningProgress::query()->updateOrCreate(
            ['user_id' => auth('web')->id(), 'content_page_id' => $contentPage->id],
            ['watched_at' => now()],
        );

        return response()->noContent();
    }

    public function syncHazardProgress(Request $request)
    {
        $validated = $request->validate([
            'content_page_ids' => ['required', 'array', 'max:500'],
            'content_page_ids.*' => ['integer', 'distinct'],
        ]);
        $contentPageIds = ContentPage::query()
            ->where('type', ContentPage::TYPE_CGI_CLIPS)
            ->whereIn('id', $validated['content_page_ids'])
            ->pluck('id');
        $timestamp = now();

        HazardLearningProgress::query()->upsert(
            $contentPageIds->map(fn ($contentPageId) => [
                'user_id' => auth('web')->id(),
                'content_page_id' => $contentPageId,
                'watched_at' => $timestamp,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->all(),
            ['user_id', 'content_page_id'],
            ['watched_at', 'updated_at'],
        );

        return response()->json([
            'watched_page_ids' => HazardLearningProgress::query()
                ->where('user_id', auth('web')->id())
                ->pluck('content_page_id'),
        ]);
    }

    public function mockTestInfo(SubSection $subSection)
    {
        return view('theory.mock_info', compact('subSection'));
    }

    public function mockTestStart(SubSection $subSection)
    {
        $videoQuestions = Question::with('choices')
            ->where('media_type', 'video')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $nonVideoQuestions = Question::with('choices')
            ->where(function ($query) {
                $query->whereNull('media_type')
                    ->orWhere('media_type', '!=', 'video');
            })
            ->inRandomOrder()
            ->limit(50 - $videoQuestions->count())
            ->get();

        // Video questions are deliberately appended so they are always the final three.
        $questions = $nonVideoQuestions
            ->concat($videoQuestions)
            ->values();

        $questions->each(fn ($question) => $question->setRelation('choices', $question->choices->shuffle()->values()));
        session(['mock_test_pass_mark' => 43, 'mock_test_duration' => 57]);

        return view('theory.mock_test', compact('subSection', 'questions') + ['mockTest' => null, 'durationMinutes' => 57]);
    }

    public function dynamicMockInfo(MockTest $mockTest)
    {
        abort_unless($mockTest->is_active, 404);
        if ($mockTest->type === 'hazard') {
            return redirect()->route('theory.hazard_mock_info');
        }

        return view('theory.mock_info', ['subSection' => $mockTest->subSection, 'mockTest' => $mockTest]);
    }

    public function dynamicMockStart(MockTest $mockTest)
    {
        abort_unless($mockTest->is_active && $mockTest->type === 'theory', 404);
        $topicIds = $mockTest->topics()->pluck('topics.id');
        $base = Question::with('choices')->when($topicIds->isNotEmpty(), fn ($q) => $q->whereIn('topic_id', $topicIds));
        $videoQuestions = (clone $base)->where('media_type', 'video')->inRandomOrder()->limit($mockTest->video_question_count)->get();
        $nonVideoQuestions = (clone $base)->where(fn ($q) => $q->whereNull('media_type')->orWhere('media_type', '!=', 'video'))->inRandomOrder()->limit(max(0, $mockTest->question_count - $videoQuestions->count()))->get();
        $questions = $nonVideoQuestions->concat($videoQuestions)->values();
        $questions->each(fn ($question) => $question->setRelation('choices', $question->choices->shuffle()->values()));
        session(['mock_test_pass_mark' => $mockTest->pass_mark, 'mock_test_duration' => $mockTest->duration_minutes]);

        return view('theory.mock_test', ['subSection' => $mockTest->subSection, 'questions' => $questions, 'mockTest' => $mockTest, 'durationMinutes' => $mockTest->duration_minutes]);
    }

    public function mockTestResult(Request $request)
    {
        $result = $request->session()->get('mock_test_result', []);
        $correct = (int) ($result['correct'] ?? $request->query('correct', 0));
        $total = (int) ($result['total'] ?? $request->query('total', 50));
        $reviews = $result['reviews'] ?? [];

        // Pass threshold is exactly 43 out of 50.
        $passMark = (int) ($result['pass_mark'] ?? $request->session()->get('mock_test_pass_mark', 43));
        $passed = $correct >= $passMark;

        return view('theory.mock_result', compact('correct', 'total', 'passed', 'reviews', 'passMark'));
    }

    public function submitMockTest(Request $request)
    {
        $validated = $request->validate([
            'question_ids' => ['required', 'array', 'min:1', 'max:50'],
            'question_ids.*' => ['integer', 'distinct', 'exists:questions,id'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['integer'],
        ]);

        $questionIds = collect($validated['question_ids'])->map(fn ($id) => (int) $id)->values();
        $questions = Question::with('choices')->whereIn('id', $questionIds)->get()->keyBy('id');
        $answers = collect($validated['answers'] ?? []);
        $correct = 0;
        $reviews = [];

        foreach ($questionIds as $questionId) {
            $question = $questions->get($questionId);
            $choiceId = (int) ($answers->get((string) $questionId, 0));

            if ($question && $question->choices->contains(fn ($choice) => $choice->id === $choiceId && $choice->is_correct)) {
                $correct++;
            } elseif ($question) {
                $selected = $question->choices->firstWhere('id', $choiceId);
                $right = $question->choices->firstWhere('is_correct', true);
                $reviews[] = [
                    'question' => $question->text_en,
                    'question_image' => $question->media_source,
                    'selected' => $selected?->text_en,
                    'selected_image' => $selected?->image_path,
                    'correct' => $right?->text_en,
                    'correct_image' => $right?->image_path,
                    'explanation' => $question->explanation_en,
                    'choices' => $question->choices->map(fn ($choice) => [
                        'text' => $choice->text_en,
                        'image' => $choice->image_path,
                        'is_correct' => (bool) $choice->is_correct,
                        'is_selected' => $choice->id === $choiceId,
                    ])->values()->all(),
                ];
            }
        }

        $total = $questionIds->count();
        $passMark = (int) $request->session()->get('mock_test_pass_mark', 43);
        $passed = $correct >= $passMark;
        $request->session()->put('mock_test_result', ['correct' => $correct, 'total' => $total, 'reviews' => $reviews, 'pass_mark' => $passMark]);

        if (auth('web')->check() && auth('web')->user()->hasVerifiedEmail()) {
            MockTestHistory::create([
                'user_id' => auth('web')->id(),
                'score' => $correct,
                'total_questions' => $total,
                'passed' => $passed,
            ]);
        }

        $redirect = route('theory.mock_test_result', [
            'correct' => $correct,
            'total' => $total,
        ]);

        return $request->expectsJson()
            ? response()->json(['redirect' => $redirect])
            : redirect()->to($redirect);
    }

    public function history()
    {
        $histories = MockTestHistory::where('user_id', auth('web')->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('theory.history', compact('histories'));
    }
}
