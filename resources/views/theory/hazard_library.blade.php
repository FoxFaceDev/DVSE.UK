<x-layouts.app :showBack="true" :backUrl="route('home')" title="Hazard learning videos">
    <div x-data="hazardLibrary(@js($watchedPageIds), @js(auth('web')->check()))" x-init="syncLocalProgress(@js(auth('web')->check() ? route('theory.hazard_progress.sync') : null))" class="space-y-8">
        <section class="overflow-hidden rounded-2xl border border-primary/15 bg-white p-5 shadow-sm" aria-label="Hazard learning progress">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary">Your progress</p>
                    <p class="mt-1 font-heading text-xl font-bold text-slate-900"><span x-text="completedCount(@js($pages->pluck('id')))"></span> of {{ $pages->count() }} clips completed</p>
                    @guest('web')<p class="mt-1 text-xs text-slate-500"><a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Sign in</a> to save your progress.</p>@endguest
                </div>
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-primary/10 font-heading text-sm font-bold text-primary" x-text="`${progressPercent(@js($pages->pluck('id')))}%`"></div>
            </div>
            <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-gradient-to-r from-primary to-sky-400 transition-all duration-500" :style="`width: ${progressPercent(@js($pages->pluck('id')))}%`"></div>
            </div>
        </section>

        <div class="grid w-full grid-cols-3 gap-2">
            @foreach(['all' => 'All videos', 'unwatched' => 'Not watched', 'watched' => 'Watched'] as $key => $label)
                <button type="button" @click="filter = '{{ $key }}'" :class="filter === '{{ $key }}' ? 'border-primary bg-primary text-white shadow-sm' : 'border-gray-200 bg-white text-gray-600'" class="min-w-0 rounded-full border px-1 py-2.5 text-xs font-bold leading-tight transition sm:px-3 sm:text-sm">{{ $label }}</button>
            @endforeach
        </div>

        <section x-show="visibleCount(@js($latestPages->pluck('id'))) > 0">
            <div class="mb-4 flex items-end justify-between gap-3">
                <div><p class="text-xs font-bold uppercase tracking-[0.18em] text-primary">New learning</p><h2 class="mt-1 font-heading text-2xl font-bold text-gray-950">Latest content</h2></div>
                <span class="text-xs font-semibold text-gray-400" x-text="`${visibleCount(@js($latestPages->pluck('id')))} clips`"></span>
            </div>
            <div class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-3 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach($latestPages as $page) @include('theory._hazard_card', ['page' => $page]) @endforeach
            </div>
        </section>

        @foreach($categoryGroups as $category => $categoryPages)
            <section x-show="visibleCount(@js($categoryPages->pluck('id'))) > 0">
                <div class="mb-4 flex items-center gap-3"><span class="h-8 w-1 rounded-full bg-primary"></span><div><h2 class="font-heading text-xl font-bold text-gray-950">{{ $category }}</h2><p class="text-xs text-gray-400" x-text="`${visibleCount(@js($categoryPages->pluck('id')))} learning clips`"></p></div></div>
                <div class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-3 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    @foreach($categoryPages as $page) @include('theory._hazard_card', ['page' => $page]) @endforeach
                </div>
            </section>
        @endforeach

        <p x-cloak x-show="visibleCount(@js($pages->pluck('id'))) === 0" class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center text-sm text-gray-500">No videos match this filter.</p>
    </div>

    <script>
        function hazardLibrary(accountWatchedIds, usesAccountProgress) {
            const localWatched = JSON.parse(localStorage.getItem('hazardWatched') || '{}');
            const watched = usesAccountProgress
                ? { ...localWatched, ...Object.fromEntries(accountWatchedIds.map(id => [id, true])) }
                : localWatched;

            return {
                filter: 'all',
                watched,
                isWatched(id) { return Boolean(this.watched[id]); },
                matches(id) {
                    return this.filter === 'all'
                        || (this.filter === 'watched' && this.isWatched(id))
                        || (this.filter === 'unwatched' && !this.isWatched(id));
                },
                visibleCount(ids) { return ids.filter(id => this.matches(id)).length; },
                completedCount(ids) { return ids.filter(id => this.isWatched(id)).length; },
                progressPercent(ids) { return ids.length ? Math.round((this.completedCount(ids) / ids.length) * 100) : 0; },
                async syncLocalProgress(url) {
                    if (!url) return;

                    const contentPageIds = Object.keys(localWatched)
                        .filter(id => localWatched[id])
                        .map(Number)
                        .filter(Number.isInteger);

                    if (!contentPageIds.length) return;

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ content_page_ids: contentPageIds }),
                        });

                        if (!response.ok) return;

                        const data = await response.json();
                        (data.watched_page_ids || []).forEach(id => { this.watched[id] = true; });
                    } catch (error) {}
                },
            };
        }
    </script>
</x-layouts.app>
