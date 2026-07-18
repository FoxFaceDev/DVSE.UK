@php
    $isEditingAd = isset($ad) && $ad;
    $allCategoryIds = $categories->pluck('id')->map(fn ($id) => (string) $id)->values();
    $defaultTargetsAll = $isEditingAd ? $ad->targets_all_categories : true;
    $targetsAll = (string) old('target_all_categories', $defaultTargetsAll ? '1' : '0') === '1';
    $defaultSelectedIds = $defaultTargetsAll
        ? $allCategoryIds
        : ($isEditingAd ? $ad->categories->pluck('id')->map(fn ($id) => (string) $id)->values() : collect());
    $selectedCategoryIds = collect(old('category_ids', $defaultSelectedIds))->map(fn ($id) => (string) $id)->unique()->values();

    if ($targetsAll) {
        $selectedCategoryIds = $allCategoryIds;
    }
@endphp

<div
    x-data="{
        all: @js($targetsAll),
        selected: @js($selectedCategoryIds),
        categoryIds: @js($allCategoryIds),
        toggleAll() {
            this.selected = this.all ? [...this.categoryIds] : [];
        },
        updateAllState() {
            this.all = this.categoryIds.length > 0 && this.selected.length === this.categoryIds.length;
        }
    }"
    class="rounded-lg border border-slate-300 bg-slate-50 p-5"
>
    <div class="mb-4 flex items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h4 class="font-bold text-slate-900">Target categories *</h4>
            <p class="mt-1 text-xs text-slate-600">Choose every category where this advertisement should appear.</p>
        </div>
        <span class="whitespace-nowrap rounded-full bg-white px-3 py-1 text-xs font-bold text-primary shadow-sm" x-text="all ? 'All categories' : `${selected.length} selected`"></span>
    </div>

    <input type="hidden" name="target_all_categories" value="0">
    <label class="mb-4 flex cursor-pointer items-center gap-3 rounded-lg border-2 border-primary/30 bg-primary/5 p-4 transition-colors hover:bg-primary/10">
        <input type="checkbox" name="target_all_categories" value="1" x-model="all" @change="toggleAll()" class="h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary">
        <span>
            <span class="block text-sm font-bold text-primary-dark">Select all categories</span>
            <span class="mt-0.5 block text-xs font-normal text-slate-600">This ad will also apply automatically to categories added later.</span>
        </span>
    </label>

    @if($categories->isNotEmpty())
        <div class="grid max-h-72 grid-cols-1 gap-2 overflow-y-auto rounded-lg border border-slate-300 bg-white p-3 md:grid-cols-2">
            @foreach($categories as $category)
                <label class="flex cursor-pointer items-start gap-3 rounded-md border border-transparent p-3 transition-colors hover:border-slate-300 hover:bg-slate-50">
                    <input
                        type="checkbox"
                        name="category_ids[]"
                        value="{{ $category->id }}"
                        x-model="selected"
                        @change="updateAllState()"
                        @checked($selectedCategoryIds->contains((string) $category->id))
                        class="mt-0.5 h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary"
                    >
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-slate-800">{{ $category->name_en }}</span>
                        @if($category->subSection)
                            <span class="mt-0.5 block truncate text-xs font-normal text-slate-500">{{ $category->subSection->name }}</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>
    @else
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">No categories exist yet. The advertisement will target all future categories.</div>
    @endif

    @error('category_ids') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
    @error('category_ids.*') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
</div>
