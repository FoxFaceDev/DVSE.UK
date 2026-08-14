@php($hazardClip = $page->clips->firstWhere('slot', 0))
<article x-show="matches({{ $page->id }})" x-transition data-hazard-card="{{ $page->id }}" data-account-watched="{{ $watchedPageIds->contains($page->id) ? 'true' : 'false' }}" class="w-[82vw] max-w-80 shrink-0 snap-start overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-[0_8px_24px_-14px_rgba(15,23,42,0.35)] transition hover:-translate-y-0.5 hover:border-primary/30 hover:shadow-lg sm:w-[calc(50%-0.5rem)]">
    <a href="{{ route('theory.hazard_study', $page) }}" class="group block">
        <div class="relative aspect-video overflow-hidden bg-slate-900">
            @if($hazardClip?->thumbnail_path)
                <img src="{{ $hazardClip->thumbnail_path }}" alt="{{ $page->admin_title ?: 'Hazard learning clip' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            @elseif($hazardClip)
                <video src="{{ $hazardClip->source }}" preload="metadata" muted class="h-full w-full object-cover"></video>
            @endif
            <span class="absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent"></span>
            <span class="absolute bottom-3 left-3 flex h-11 w-11 items-center justify-center rounded-full bg-primary text-white shadow-lg ring-4 ring-white/25"><svg class="ml-0.5 h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
            <span x-cloak x-show="isWatched({{ $page->id }})" class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-green-600 px-3 py-1.5 text-xs font-bold text-white shadow"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m5 12 4 4L19 6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>Watched</span>
        </div>
        <div class="p-4">
            <div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="text-[11px] font-bold uppercase tracking-wider text-primary">{{ $page->library_category ?: 'Hazard learning' }}</p><h3 class="mt-1 line-clamp-2 font-heading text-base font-bold leading-snug text-gray-950">{{ $page->admin_title ?: 'Hazard clip' }}</h3></div><span class="mt-1 text-primary transition group-hover:translate-x-1">&rarr;</span></div>
            <p x-show="!isWatched({{ $page->id }})" class="mt-3 text-xs font-medium text-gray-400">Start hazard exercise</p>
            <p x-cloak x-show="isWatched({{ $page->id }})" class="mt-3 text-xs font-medium text-green-700">Learning completed</p>
        </div>
    </a>
</article>
