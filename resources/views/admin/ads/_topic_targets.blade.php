@php
    $isEditingAd = isset($ad) && $ad;
    $allTopicIds = $topics->pluck('id')->map(fn ($id) => (string) $id)->values();
    $defaultTargetsAll = $isEditingAd ? $ad->targets_all_topics : true;
    $targetsAll = (string) old('target_all_topics', $defaultTargetsAll ? '1' : '0') === '1';
    $defaultSelectedIds = $defaultTargetsAll ? $allTopicIds : ($isEditingAd ? $ad->topics->pluck('id')->map(fn ($id) => (string) $id) : collect());
    $selectedTopicIds = collect(old('topic_ids', $defaultSelectedIds))->map(fn ($id) => (string) $id)->unique()->values();
    if ($targetsAll) $selectedTopicIds = $allTopicIds;
@endphp

<div x-data="{ all: @js($targetsAll), selected: @js($selectedTopicIds), ids: @js($allTopicIds), toggleAll() { this.selected = this.all ? [...this.ids] : [] }, updateAll() { this.all = this.ids.length > 0 && this.selected.length === this.ids.length } }" class="rounded-lg border border-slate-300 bg-slate-50 p-5">
    <div class="mb-4 flex items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div><h4 class="font-bold text-slate-900">Target topics *</h4><p class="mt-1 text-xs text-slate-600">Choose the question topics where this ad may appear.</p></div>
        <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-primary shadow-sm" x-text="all ? 'All topics' : `${selected.length} selected`"></span>
    </div>
    <input type="hidden" name="target_all_topics" value="0">
    <label class="mb-4 flex cursor-pointer items-center gap-3 rounded-lg border-2 border-primary/30 bg-primary/5 p-4">
        <input type="checkbox" name="target_all_topics" value="1" x-model="all" @change="toggleAll()" class="h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary">
        <span><span class="block text-sm font-bold text-primary-dark">Select all topics</span><span class="text-xs text-slate-600">This replaces the old “Select all categories” targeting and also includes topics added later.</span></span>
    </label>
    <div class="grid max-h-80 grid-cols-1 gap-2 overflow-y-auto rounded-lg border bg-white p-3 md:grid-cols-2">
        @forelse($topics as $topic)
            <label class="flex cursor-pointer items-start gap-3 rounded-md p-3 hover:bg-slate-50">
                <input type="checkbox" name="topic_ids[]" value="{{ $topic->id }}" x-model="selected" @change="updateAll()" @checked($selectedTopicIds->contains((string) $topic->id)) class="mt-0.5 h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary">
                <span><span class="block text-sm font-semibold text-slate-800">{{ $topic->name_en }}</span><span class="text-xs text-slate-500">{{ class_basename($topic->topicable_type) }}: {{ $topic->topicable?->name_en ?? $topic->topicable?->name ?? 'Unknown' }}</span></span>
            </label>
        @empty
            <p class="p-3 text-sm text-amber-700">No topics exist yet.</p>
        @endforelse
    </div>
    @error('topic_ids')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    @error('topic_ids.*')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
</div>
