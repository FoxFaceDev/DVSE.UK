@props(['topic', 'color' => null])
@php
    $meta = $topic->questions_count.' '.Str::plural('question', $topic->questions_count);
    if ($topic->content_pages_count) {
        $meta .= ' · '.$topic->content_pages_count.' learning '.Str::plural('page', $topic->content_pages_count);
    }
@endphp
<x-learning-card
    :href="$topic->cgi_content_pages_count ? route('theory.hazard_library', $topic) : route('theory.practice', $topic)"
    :name="$topic->name_en"
    :translation="$topic->name_ku"
    :color="$color"
    :meta="$meta"
    :icon="$topic->cgi_content_pages_count ? 'play' : 'book'"
/>
