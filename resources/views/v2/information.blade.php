<x-layouts.app :title="$heading">
    <section class="v2-panel"><h1 class="text-2xl font-bold mb-4">{{ $heading }}</h1><p>{{ $description }}</p>
        @foreach($topics ?? [] as $topic)<a class="v2-link" href="{{ route('theory.hazard_library', $topic) }}">{{ $topic->name_en }} · {{ $topic->content_pages_count }} clips</a>@endforeach
        @foreach($ads ?? [] as $ad)<a class="v2-link" href="{{ $ad->link_url }}" target="_blank" rel="noopener sponsored">@if($ad->media_type === 'video')<video controls src="{{ $ad->media_source }}"></video>@else<img src="{{ $ad->media_source }}" alt="{{ $ad->title }}">@endif{{ $ad->title }}</a>@endforeach
    </section>
</x-layouts.app>
