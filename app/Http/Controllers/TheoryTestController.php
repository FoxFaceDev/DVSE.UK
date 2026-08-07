<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Language;
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
        $ads = Ad::where('is_active', true)
            ->whereNotNull('language_id')
            ->where(function ($query) use ($topic) {
                $query->where('targets_all_categories', true);
                if ($topic->topicable_type === 'App\Models\Category') {
                    $query->orWhereHas('categories', fn ($categoryQuery) => $categoryQuery->where('categories.id', $topic->topicable_id));
                }
            })
            ->inRandomOrder()
            ->get();

        $languages = Language::active()->get();

        return view('theory.practice', compact('topic', 'practiceItems', 'ads', 'languages'));
    }

    public function result()
    {
        return view('theory.result');
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

        $languages = Language::active()->get();

        return view('theory.mock_test', compact('subSection', 'questions', 'languages'));
    }

    public function mockTestResult(Request $request)
    {
        $result = $request->session()->get('mock_test_result', []);
        $correct = (int) ($result['correct'] ?? $request->query('correct', 0));
        $total = (int) ($result['total'] ?? $request->query('total', 50));
        $reviews = $result['reviews'] ?? [];

        // Pass threshold is exactly 43 out of 50.
        $passed = $correct >= 43;

        return view('theory.mock_result', compact('correct', 'total', 'passed', 'reviews'));
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
                ];
            }
        }

        $total = $questionIds->count();
        $passed = $correct >= 43;
        $request->session()->put('mock_test_result', compact('correct', 'total', 'reviews'));

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
