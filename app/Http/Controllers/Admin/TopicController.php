<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::with('topicable')->get();

        return view('admin.topics.index', compact('topics'));
    }

    public function create(Request $request)
    {
        $topicableType = $request->query('topicable_type', Section::class);
        $topicableId = $request->query('topicable_id');

        return view('admin.topics.create', [
            'topicableType' => $topicableType,
            'topicableId' => $topicableId,
            'parentOptions' => $this->parentOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedTopic($request);

        Topic::create($validated);

        return redirect()->route('admin.topics.index')->with('success', 'Topic created successfully.');
    }

    public function edit(Topic $topic)
    {
        return view('admin.topics.edit', [
            'topic' => $topic,
            'parentOptions' => $this->parentOptions(),
        ]);
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $this->validatedTopic($request);

        $topic->update($validated);

        return redirect()->route('admin.topics.index')->with('success', 'Topic updated successfully.');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();

        return redirect()->back()->with('success', 'Topic deleted successfully.');
    }

    private function validatedTopic(Request $request): array
    {
        $parentModels = [Section::class, SubSection::class, Category::class];
        $validator = Validator::make($request->all(), [
            'topicable_type' => ['required', 'string', Rule::in($parentModels)],
            'topicable_id' => ['required', 'integer'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ku' => ['nullable', 'string', 'max:255'],
        ]);
        $validated = $validator->validate();
        $parentModel = $validated['topicable_type'];

        if (! $parentModel::query()->whereKey($validated['topicable_id'])->exists()) {
            throw ValidationException::withMessages([
                'topicable_id' => 'The selected parent does not exist.',
            ]);
        }

        return $validated;
    }

    private function parentOptions(): array
    {
        return [
            Section::class => Section::query()
                ->orderBy('name')
                ->get()
                ->map(fn (Section $section) => [
                    'id' => $section->id,
                    'label' => $section->name,
                ])
                ->all(),
            SubSection::class => SubSection::query()
                ->with('section')
                ->orderBy('name')
                ->get()
                ->map(fn (SubSection $subSection) => [
                    'id' => $subSection->id,
                    'label' => $subSection->name.' — '.$subSection->section?->name,
                ])
                ->all(),
            Category::class => Category::query()
                ->with('subSection.section')
                ->orderBy('name_en')
                ->get()
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'label' => $category->name_en.' — '.$category->subSection?->name,
                ])
                ->all(),
        ];
    }
}
