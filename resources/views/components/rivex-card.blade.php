@props(['href', 'name', 'description' => null, 'color' => null, 'image' => null, 'fallbackImage', 'compact' => false, 'index' => 0])
@php($cardColor = preg_match('/^#[0-9a-fA-F]{6}$/', $color ?? '') ? $color : ['#1664f5', '#f7b500', '#13b8c8', '#ef4455'][$index % 4])
<li><a href="{{ $href }}" class="rivex-card {{ $compact ? 'rivex-card--compact' : '' }}" style="--rivex-card-color: {{ $cardColor }}; --rivex-card-order: {{ min($index, 8) }}">
    <img class="rivex-card__icon" src="{{ $image ?: asset('images/rivex/'.$fallbackImage) }}" alt="" loading="{{ $index > 3 ? 'lazy' : 'eager' }}">
    <span class="rivex-card__body"><span class="rivex-card__title">{{ $name }}</span>@if(!$compact)<span class="rivex-card__rule" aria-hidden="true"></span><span class="rivex-card__text">{{ $description ?: 'Choose your next step in your learning journey.' }}</span>@elseif($description)<span class="rivex-visually-hidden">{{ $description }}</span>@endif</span>
</a></li>
