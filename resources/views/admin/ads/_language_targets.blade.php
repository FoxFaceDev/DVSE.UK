@php
    $savedLanguageIds = isset($ad) && $ad
        ? $ad->languages->pluck('id')->map(fn ($id) => (string) $id)
        : $languages->where('is_active', true)->pluck('id')->map(fn ($id) => (string) $id);
    $selectedLanguageIds = collect(old('language_ids', $savedLanguageIds))->map(fn ($id) => (string) $id);
@endphp

<div class="rounded-lg border border-slate-300 bg-slate-50 p-5">
    <input type="hidden" name="language_filter_present" value="1">
    <h4 class="font-bold text-slate-900">Languages *</h4>
    <p class="mt-1 text-xs text-slate-600">The advertisement is shown only to learners using one of the checked languages.</p>
    <div class="mt-3 grid gap-2 sm:grid-cols-2">
        @foreach($languages as $language)
            <label class="flex cursor-pointer items-center gap-3 rounded-md border bg-white p-3">
                <input type="checkbox" name="language_ids[]" value="{{ $language->id }}" @checked($selectedLanguageIds->contains((string) $language->id)) class="h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary">
                <span class="text-sm font-semibold text-slate-800">{{ $language->name }}{{ $language->is_active ? '' : ' (Inactive)' }}</span>
            </label>
        @endforeach
    </div>
    @error('language_ids')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
    @error('language_ids.*')<p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
</div>
