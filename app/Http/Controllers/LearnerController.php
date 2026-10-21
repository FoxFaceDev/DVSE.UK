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
use App\Support\HazardPage;
use Illuminate\Http\Request;

class LearnerController extends Controller
{
    public function theory(Request $request)
    {
        $validated = $request->validate(['section' => ['nullable', 'integer', 'exists:sections,id']]);
        $section = isset($validated['section'])
            ? Section::findOrFail($validated['section'])
            : (Section::whereNull('home_key')->whereRaw('lower(name) like ?', ['%theory%'])->latest('id')->first()
                ?? Section::where('home_key', 'theory')->first()
                ?? Section::whereRaw('lower(name) like ?', ['%theory%'])->first());

        $section?->load(['subSections', 'topics' => fn ($query) => $query->with('questions:id,topic_id')->withCount([
            'contentPages as cgi_content_pages_count' => fn ($pages) => $pages->where('type', 'cgi_clips'),
        ])]);

        return view('v2.theory', compact('section'));
    }

    public function revision(Request $request, ?Category $revisionGroup = null)
    {
        $validated = $request->validate(['group' => ['nullable', 'integer', 'exists:categories,id']]);
        $revisionGroup ??= isset($validated['group']) ? Category::findOrFail($validated['group'])
            : Category::where('page_type', 'revision')->orderBy('id')->first();
        $categories = Topic::questionCategories()
            ->when($revisionGroup, fn ($query) => $query->inRevisionGroup($revisionGroup->id))
            ->withCount('questions')->with('questions:id,topic_id')->ordered()->get();

        return view('v2.revision', compact('categories', 'revisionGroup'));
    }

    public function revise(Request $request)
    {
        $groupData = $request->validate(['group' => ['nullable', 'integer', 'exists:categories,id']]);
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
        $categoryQuery = Topic::questionCategories()
            ->when(isset($groupData['group']), fn ($query) => $query->inRevisionGroup((int) $groupData['group']));
        if ($request->has('categories') || $request->has('topics')) {
            $categoryQuery->whereIn('id', $selectedCategoryIds);
        }
        $categoryIds = $categoryQuery->pluck('id');
        $questions = Question::with('choices')->whereIn('topic_id', $categoryIds)->get();
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
        $validated = $request->validate(['subsection' => ['nullable', 'integer', 'exists:sub_sections,id']]);
        $subSection = isset($validated['subsection']) ? SubSection::findOrFail($validated['subsection']) : SubSection::where('page_type', 'hazard')->first();
        if ($subSection) {
            abort_unless($subSection->isHazardPerception(), 404);
        }
        if ($request->boolean('clips')) {
            $topics = Topic::whereHas('contentPages', fn ($q) => $q->where('type', 'cgi_clips'))
                ->when($subSection, fn ($query) => $query->where(fn ($parent) => $parent
                    ->where(fn ($direct) => $direct->where('topicable_type', SubSection::class)->where('topicable_id', $subSection->id))
                    ->orWhere(fn ($group) => $group->where('topicable_type', Category::class)->whereIn('topicable_id', $subSection->categories()->select('id')))))
                ->withCount('contentPages')->ordered()->get();

            return view('v2.information', ['heading' => $request->query('mode') === 'help' ? 'Hazard Perception Help Videos' : 'Hazard Perception Clips', 'description' => 'Choose a topic to practise recognising developing hazards.', 'topics' => $topics]);
        }

        return $this->hazardPage($request, $subSection);
    }

    public function hazardPage(Request $request, ?SubSection $subSection = null)
    {
        $language = $request->query('language', auth('web')->user()?->preferredLanguage?->code ?? 'en');
        $language = in_array($language, ['ku', 'ckb'], true) ? 'ku' : 'en';

        return view('v2.hazard-menu', HazardPage::viewData($subSection, $language));
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
