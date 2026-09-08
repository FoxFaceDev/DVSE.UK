@props(['href', 'name', 'description' => null, 'color' => null, 'image' => null, 'index' => 0])
@php
    $cardColor = preg_match('/^#[0-9a-fA-F]{6}$/', $color ?? '') ? $color : '#245aa2';
    $icon = 'book';
@endphp
<a href="{{ $href }}" class="mode-card" style="--card-order: {{ min($index, 6) }}; --mode-dark: color-mix(in srgb, {{ $cardColor }} 45%, #102438); --mode-light: color-mix(in srgb, {{ $cardColor }} 70%, #102438); --mode-accent: #ffffff">
    <div class="mode-copy">
        <span class="eyebrow">EXPLORE &amp; LEARN</span>
        <h2>{{ $name }}</h2>
        <p>{{ $description ?: 'Choose your next step in your learning journey.' }}</p>
        <span class="mode-link">Explore <x-study-icon type="arrow" /></span>
    </div>
<div class="mode-art" aria-hidden="true"><span class="art-orbit"></span><div class="art-tile" x-data="{ imageFailed: false }" x-init="$nextTick(() => { if ($refs.icon && $refs.icon.complete && !$refs.icon.naturalWidth) imageFailed = true })">@if($image)<img x-ref="icon" src="{{ $image }}" alt="" x-show="!imageFailed" x-on:error="imageFailed = true"><x-study-icon :type="$icon" x-show="imageFailed" x-cloak />@else<x-study-icon :type="$icon" />@endif</div><span class="art-spark">+</span></div>
</a>
