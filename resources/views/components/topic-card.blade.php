@props(['topic', 'color' => null])
@php
    $href = $topic->isLearningCategory() && $topic->cgi_content_pages_count
        ? route('theory.hazard_library', $topic)
        : route('theory.practice', $topic);
@endphp
<x-category-card :category="$topic" :href="$href" :color="$color" />
