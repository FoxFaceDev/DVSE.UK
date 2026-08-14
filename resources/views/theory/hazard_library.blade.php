<x-layouts.app :showBack="true" :backUrl="route('home')" title="Hazard learning videos">
    <div x-data="hazardLibrary()" class="space-y-8">
        <div class="flex gap-2 overflow-x-auto pb-1">
            @foreach(['all' => 'All videos', 'unwatched' => 'Not watched', 'watched' => 'Watched'] as $key => $label)
                <button type="button" @click="filter = '{{ $key }}'" :class="filter === '{{ $key }}' ? 'border-primary bg-primary text-white shadow-sm' : 'border-gray-200 bg-white text-gray-600'" class="whitespace-nowrap rounded-full border px-4 py-2 text-sm font-bold transition">{{ $label }}</button>
            @endforeach
        </div>

        <section x-show="visibleCount(@js($latestPages->pluck('id'))) > 0">
            <div class="mb-4 flex items-end justify-between gap-3">
                <div><p class="text-xs font-bold uppercase tracking-[0.18em] text-primary">New learning</p><h2 class="mt-1 font-heading text-2xl font-bold text-gray-950">Latest content</h2></div>
                <span class="text-xs font-semibold text-gray-400" x-text="`${visibleCount(@js($latestPages->pluck('id')))} clips`"></span>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach($latestPages as $page) @include('theory._hazard_card', ['page' => $page]) @endforeach
            </div>
        </section>

        @foreach($categoryGroups as $category => $categoryPages)
            <section x-show="visibleCount(@js($categoryPages->pluck('id'))) > 0">
                <div class="mb-4 flex items-center gap-3"><span class="h-8 w-1 rounded-full bg-primary"></span><div><h2 class="font-heading text-xl font-bold text-gray-950">{{ $category }}</h2><p class="text-xs text-gray-400" x-text="`${visibleCount(@js($categoryPages->pluck('id')))} learning clips`"></p></div></div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($categoryPages as $page) @include('theory._hazard_card', ['page' => $page]) @endforeach
                </div>
            </section>
        @endforeach

        <p x-cloak x-show="visibleCount(@js($pages->pluck('id'))) === 0" class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center text-sm text-gray-500">No videos match this filter.</p>
    </div>

    <script>
        function hazardLibrary() {
            return {
                filter: 'all',
                watched: JSON.parse(localStorage.getItem('hazardWatched') || '{}'),
                isWatched(id) { return Boolean(this.watched[id]); },
                matches(id) {
                    return this.filter === 'all'
                        || (this.filter === 'watched' && this.isWatched(id))
                        || (this.filter === 'unwatched' && !this.isWatched(id));
                },
                visibleCount(ids) { return ids.filter(id => this.matches(id)).length; },
            };
        }
    </script>
</x-layouts.app>
