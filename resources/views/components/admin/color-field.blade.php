@props(['name' => 'color', 'value' => '#0452bc', 'label' => 'Card Color', 'required' => true, 'errorKey' => null])
@php
    $fieldId = 'color-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $name).'-'.Str::random(6);
    $validationKey = $errorKey ?? $name;
@endphp
<div x-data="cardColorField(@js($value))">
    <label for="{{ $fieldId }}" class="mb-1 block text-sm font-medium text-gray-700">{{ $label }}{{ $required ? ' *' : '' }}</label>
    <div class="flex items-center gap-3">
        <input type="color" :value="picker" @input="choose($event.target.value)" aria-label="{{ $label }} picker" class="h-11 w-12 shrink-0 cursor-pointer rounded-lg border border-gray-300 bg-white p-1">
        <input id="{{ $fieldId }}" type="text" name="{{ $name }}" value="{{ $value }}" x-model="code" @input="syncPicker()" @required($required) maxlength="100" spellcheck="false" autocapitalize="off" aria-describedby="{{ $fieldId }}-help" class="min-w-0 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-primary focus:ring-primary" placeholder="#f7b500 or rgb(247, 181, 0)">
    </div>
    <p id="{{ $fieldId }}-help" class="mt-1 text-xs text-gray-500">HEX, RGB, or HSL, e.g. #f7b500, rgb(247, 181, 0), hsl(44, 100%, 48%).</p>
    @error($validationKey)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
