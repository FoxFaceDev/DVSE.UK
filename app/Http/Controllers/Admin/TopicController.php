<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;
use App\Rules\SafeIconFile;
use App\Support\MediaStorage;
use App\Support\RevisionCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        $revisionMode = $request->boolean('revision');
        $revisionGroup = $revisionMode ? Category::where('page_type', 'revision')->orderBy('id')->first() : null;
        $topics = Topic::with('topicable')
            ->when($revisionMode, fn ($query) => $query->questionCategories())
            ->when($revisionGroup, fn ($query) => $query->where('topicable_type', Category::class)->where('topicable_id', $revisionGroup->id))
            ->when($revisionMode && ! $revisionGroup, fn ($query) => $query->whereRaw('1 = 0'))
            ->orderBy('topicable_type')->orderBy('topicable_id')->ordered()->get();

        return view('admin.topics.index', compact('topics', 'revisionMode', 'revisionGroup'));
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

        if ($request->hasFile('icon')) {
            $validated['icon_path'] = MediaStorage::store($request->file('icon'), 'icons/categories');
        }

        $category = Topic::create($validated);
        RevisionCatalog::connectEntry($category);

        return redirect()->to($this->parentAdminUrl($category))->with('success', 'Category created successfully. You can add another category here.');
    }

    public function edit(Request $request, Topic $topic)
    {
        $returnTo = $this->safeAdminReturnUrl(url()->previous(), $request)
            ?? $this->parentAdminUrl($topic);

        return view('admin.topics.edit', [
            'topic' => $topic,
            'parentOptions' => $this->parentOptions(),
            'returnTo' => $returnTo,
        ]);
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $this->validatedTopic($request, $topic);
        $parentChanged = $topic->topicable_type !== $validated['topicable_type']
            || (int) $topic->topicable_id !== (int) $validated['topicable_id'];

        if ($parentChanged) {
            $validated['position'] = (int) Topic::query()
                ->where('topicable_type', $validated['topicable_type'])
                ->where('topicable_id', $validated['topicable_id'])
                ->max('position') + 1;
        }

        if ($request->hasFile('icon')) {
            MediaStorage::delete($topic->getRawOriginal('icon_path'));
            $validated['icon_path'] = MediaStorage::store($request->file('icon'), 'icons/categories');
        } elseif ($request->boolean('remove_icon')) {
            MediaStorage::delete($topic->getRawOriginal('icon_path'));
            $validated['icon_path'] = null;
        }

        $topic->update($validated);
        RevisionCatalog::connectEntry($topic);

        $returnTo = $this->safeAdminReturnUrl($request->input('return_to'), $request)
            ?? $this->parentAdminUrl($topic);

        return redirect()->to($returnTo)->with('success', 'Category updated successfully.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'category_ids' => ['required', 'array', 'min:1', 'max:500'],
            'category_ids.*' => ['required', 'integer', 'distinct', 'exists:topics,id'],
        ]);

        $categories = Topic::query()->whereIn('id', $validated['category_ids'])->get();
        if ($categories->count() !== count($validated['category_ids'])
            || $categories->pluck('topicable_type')->unique()->count() !== 1
            || $categories->pluck('topicable_id')->unique()->count() !== 1) {
            throw ValidationException::withMessages([
                'category_ids' => 'Categories can only be reordered inside the same location.',
            ]);
        }

        $parent = $categories->first();
        $allSiblingIds = Topic::query()
            ->where('topicable_type', $parent->topicable_type)
            ->where('topicable_id', $parent->topicable_id)
            ->pluck('id');

        if ($allSiblingIds->count() !== count($validated['category_ids'])
            || $allSiblingIds->diff($validated['category_ids'])->isNotEmpty()) {
            throw ValidationException::withMessages([
                'category_ids' => 'The complete category list is required to save its order.',
            ]);
        }

        DB::transaction(function () use ($validated) {
            foreach ($validated['category_ids'] as $index => $categoryId) {
                Topic::query()->whereKey($categoryId)->update(['position' => $index + 1]);
            }
        });

        return response()->json(['message' => 'Category order saved.']);
    }

    public function destroy(Topic $topic)
    {
        MediaStorage::delete($topic->getRawOriginal('icon_path'));
        $topic->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    private function validatedTopic(Request $request, ?Topic $topic = null): array
    {
        $parentModels = [Section::class, SubSection::class, Category::class];
        $validator = Validator::make($request->all(), [
            'topicable_type' => ['required', 'string', Rule::in($parentModels)],
            'topicable_id' => ['required', 'integer'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ku' => ['nullable', 'string', 'max:255'],
            'content_type' => ['required', Rule::in([Topic::TYPE_QUESTIONS, Topic::TYPE_LEARNING_PAGES])],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'icon' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048', new SafeIconFile],
            'icon_preset' => ['nullable', Rule::in(array_keys(RevisionCatalog::icons()))],
            'remove_icon' => ['nullable', 'boolean'],
        ]);
        $validated = $validator->validate();
        unset($validated['icon'], $validated['remove_icon']);
        $parentModel = $validated['topicable_type'];

        if (! $parentModel::query()->whereKey($validated['topicable_id'])->exists()) {
            throw ValidationException::withMessages([
                'topicable_id' => 'The selected parent does not exist.',
            ]);
        }

        if ($topic && $validated['content_type'] !== $topic->content_type) {
            $hasIncompatibleContent = $validated['content_type'] === Topic::TYPE_QUESTIONS
                ? $topic->contentPages()->exists()
                : $topic->questions()->exists();

            if ($hasIncompatibleContent) {
                throw ValidationException::withMessages([
                    'content_type' => 'Remove the existing content before changing this category type.',
                ]);
            }
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

    private function parentAdminUrl(Topic $category): string
    {
        $parent = $category->topicable;

        return match (true) {
            $parent instanceof Section => route('admin.sections.show', $parent),
            $parent instanceof SubSection => route('admin.sections.sub_sections.show', [$parent->section_id, $parent]),
            $parent instanceof Category => route('admin.sections.sub_sections.categories.show', [
                $parent->subSection->section_id,
                $parent->sub_section_id,
                $parent,
            ]),
            default => route('admin.topics.index'),
        };
    }

    private function safeAdminReturnUrl(mixed $url, Request $request): ?string
    {
        if (! is_string($url) || $url === '' || strlen($url) > 2048) {
            return null;
        }

        $parts = parse_url($url);
        if ($parts === false || isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        if (isset($parts['host']) && strcasecmp($parts['host'], $request->getHost()) !== 0) {
            return null;
        }

        if (isset($parts['scheme']) && ! in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return null;
        }

        if (isset($parts['port']) && $parts['port'] !== $request->getPort()) {
            return null;
        }

        $path = $parts['path'] ?? '';
        if ($path !== '/admin' && ! str_starts_with($path, '/admin/')) {
            return null;
        }

        return $url;
    }
}
