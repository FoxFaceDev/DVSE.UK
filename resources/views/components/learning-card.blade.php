@props(['href', 'name', 'translation' => null, 'color' => null, 'kind' => 'Topic', 'meta' => null, 'icon' => 'book'])
@php
    $cardColor = preg_match('/^#[0-9a-fA-F]{6}$/', $color ?? '') ? $color : '#245aa2';
@endphp
<a href="{{ $href }}" class="learning-card" style="--learning-color: {{ $cardColor }}">
    <span class="learning-card-icon"><x-study-icon :type="$icon" /></span>
    <div class="learning-card-copy">
        <span class="eyebrow">{{ $kind }}</span>
        <h3>{{ $name }}</h3>
        @if($translation)
            <p class="learning-card-translation" lang="ku" dir="rtl">{{ $translation }}</p>
        @endif
        @if($meta)
            <p class="learning-card-meta">{{ $meta }}</p>
        @endif
    </div>
    <span class="learning-card-arrow"><x-study-icon type="arrow" /></span>
</a>
