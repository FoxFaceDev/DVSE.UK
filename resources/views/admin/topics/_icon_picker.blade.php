@php($selectedIcon = old('icon_preset', $topic->icon_preset ?? 'all-categories'))
<div x-data="{ preset: @js($selectedIcon ?: 'all-categories') }" class="rounded-lg border border-slate-200 bg-slate-50 p-4">
    <label for="icon-preset" class="mb-2 block text-sm font-semibold text-slate-700">Practice design icon</label>
    <div class="flex items-center gap-4">
        <img :src="@js(asset('design-v2/Practice/img/practice')).replace(/\/$/, '') + '/' + preset + '.svg'" alt="Selected icon preview" class="h-14 w-14 object-contain">
        <select id="icon-preset" name="icon_preset" x-model="preset" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2">
            @foreach(\App\Support\RevisionCatalog::icons() as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
    </div>
    <p class="mt-2 text-xs text-slate-500">Choose one of the supplied SVG icons, or upload your own above. An uploaded icon takes priority.</p>
    @if(isset($topic) && $topic->getRawOriginal('icon_path'))
        <label class="mt-3 flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="remove_icon" value="1">Remove the uploaded icon and use the selected design icon</label>
    @endif
    @error('icon_preset')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
