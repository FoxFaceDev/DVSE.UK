<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Language;
use App\Models\MockTest;
use App\Models\Question;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;
use Illuminate\Http\Request;

class LearnerController extends Controller
{
    public function theory(Request $request)
    {
        $validated = $request->validate(['section' => ['nullable', 'integer', 'exists:sections,id']]);
        $section = isset($validated['section'])
            ? Section::findOrFail($validated['section'])
            : Section::whereRaw('lower(name) like ?', ['%theory%'])->first();

        $section?->load(['subSections', 'topics' => fn ($query) => $query->with('questions:id,topic_id')->withCount([
            'contentPages as cgi_content_pages_count' => fn ($pages) => $pages->where('type', 'cgi_clips'),
        ])]);

        return view('v2.theory', compact('section'));
    }

    public function revision(Request $request)
    {
        $categories = Topic::questionCategories()->withCount('questions')->with('questions:id,topic_id')->ordered()->get();

        return view('v2.revision', compact('categories'));
    }

    public function revise(Request $request)
    {
        if ($request->has('categories')) {
            $validated = $request->validate([
                'categories' => ['nullable', 'array', 'max:200'],
                'categories.*' => ['integer', 'exists:topics,id'],
            ]);
            $selectedCategoryIds = $validated['categories'] ?? [];
        } else {
            $validated = $request->validate([
                'topics' => ['nullable', 'array', 'max:200'],
                'topics.*' => ['integer', 'exists:topics,id'],
            ]);
            $selectedCategoryIds = $validated['topics'] ?? [];
        }
        $categoryIds = Topic::questionCategories()->whereIn('id', $selectedCategoryIds)->pluck('id');
        $questions = Question::with('choices')->when($categoryIds->isNotEmpty(), fn ($query) => $query->whereIn('topic_id', $categoryIds))->get();
        $questions->each(fn ($question) => $question->setRelation('choices', $question->choices->shuffle()->values()));

        return $this->quiz($questions, $request->routeIs('v2.flagged'));
    }

    public function quiz($questions, bool $flagged = false)
    {
        $languages = Language::active()->get();
        $preferredLanguage = auth('web')->user()?->preferredLanguage;
        $topicIds = $questions->pluck('topic_id')->unique();
        $categoryIds = Topic::whereIn('id', $topicIds)->where('topicable_type', Category::class)->pluck('topicable_id');
        $ads = Ad::currentlyRunning()->where('display_type', 'question')
            ->forLanguage($preferredLanguage?->id ?? Language::where('code', 'en')->value('id'))
            ->where(function ($query) use ($topicIds, $categoryIds) {
                $query->where('targets_all_topics', true)
                    ->orWhere(fn ($legacy) => $legacy->where('targets_all_categories', true)->whereDoesntHave('topics'))
                    ->orWhereHas('topics', fn ($topics) => $topics->whereIn('topics.id', $topicIds))
                    ->orWhere(fn ($legacy) => $legacy->where('targets_all_topics', false)->whereHas('categories', fn ($categories) => $categories->whereIn('categories.id', $categoryIds)));
            })->with('languages:id,name,code')->get();

        return view('v2.quiz', compact('questions', 'languages', 'preferredLanguage', 'flagged', 'ads'));
    }

    public function mock()
    {
        $mockTest = MockTest::where('is_active', true)->where('type', 'theory')->first();
        if ($mockTest) {
            return redirect()->route('theory.dynamic_mock_info', $mockTest);
        }
        $subSection = SubSection::whereRaw('lower(name) like ?', ['%theory%'])->first() ?? SubSection::first();
        if ($subSection) {
            return redirect()->route('theory.mock_test_info', $subSection);
        }

        return $this->information('Mock Test', 'A mock test will be available once the question bank has been set up.');
    }

    public function hazards(Request $request)
    {
        $topics = Topic::whereHas('contentPages', fn ($q) => $q->where('type', 'cgi_clips'))->withCount('contentPages')->ordered()->get();
        if ($request->boolean('clips')) {
            return view('v2.information', ['heading' => 'Hazard Perception Clips', 'description' => 'Choose a topic to practise recognising developing hazards.', 'topics' => $topics]);
        }

        return view($request->query('language') === 'ku' ? 'v2.hazard-menu-ku' : 'v2.hazard-menu');
    }

    public function search(Request $request)
    {
        $request->validate(['q' => ['nullable', 'string', 'max:200']]);
        $term = trim($request->query('q', ''));
        $questions = Question::with('topic')->when($term !== '', fn ($query) => $query->where(fn ($q) => $q->where('text_en', 'like', '%'.$term.'%')->orWhere('text_ku', 'like', '%'.$term.'%')))->paginate(20)->withQueryString();

        return view('v2.search', compact('questions', 'term'));
    }

    public function progress()
    {
        $histories = auth('web')->check() ? auth('web')->user()->mockTestHistories()->latest()->limit(20)->get() : collect();

        return view('v2.progress', compact('histories'));
    }

    public function offers()
    {
        $ads = Ad::currentlyRunning()->where('display_type', 'site')->forLanguage(auth('web')->user()?->preferred_language_id ?? Language::where('code', 'en')->value('id'))->get();

        return view('v2.information', ['heading' => 'Offers', 'description' => $ads->isEmpty() ? 'There are no offers available right now. Check back soon.' : 'Discover the latest offers from our partners.', 'ads' => $ads]);
    }

    public function information(string $heading, string $description)
    {
        return view('v2.information', compact('heading', 'description'));
    }
}
