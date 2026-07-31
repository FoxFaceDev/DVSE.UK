<x-layouts.app :showBack="true" :backUrl="route('frontend.section', $subSection->section_id)" title="{{ $subSection->name }}">
    <div class="space-y-4">
        @if(Str::lower($subSection->name) === 'hazard perception')
            <a href="{{ route('theory.hazard_mock_info') }}" class="group relative block w-full overflow-hidden rounded-2xl bg-primary p-6 text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:bg-primary-dark hover:shadow-xl">
                <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 blur-2xl transition-transform duration-500 group-hover:scale-150"></div>
                <div class="absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-black/10 blur-xl"></div>

                <div class="absolute right-6 top-0 rounded-b-lg border border-white/30 bg-white/20 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm backdrop-blur-sm">
                    Official Mock Test
                </div>

                <div class="relative z-10 mt-2 flex items-center gap-5">
                    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-xl border border-white/30 bg-white/20 shadow-inner backdrop-blur-md transition-colors duration-300 group-hover:bg-white/30">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.9 2.7 17a2 2 0 0 0 1.73 3h15.14a2 2 0 0 0 1.73-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="font-heading text-xl font-bold tracking-tight text-white transition-transform group-hover:translate-x-1">Hazard Mock Test</h3>
                        <p class="mb-3 mt-1 text-sm font-medium text-white/80">14 Clips &bull; 15 Minutes</p>
                        <div class="h-1 w-12 rounded-full bg-white/30 transition-all duration-500 group-hover:w-full"></div>
                    </div>

                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition-all duration-300 group-hover:translate-x-1 group-hover:bg-white group-hover:text-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
        @endif

        @if($subSection->categories->isNotEmpty())
            <section>
                <h2 class="mb-4 font-heading text-xl font-bold" style="color: {{ $subSection->color ?? '#1e293b' }}">Categories</h2>
                <div class="grid grid-cols-1 gap-3">
                    @foreach($subSection->categories as $category)
                        <a href="{{ route('frontend.category', $category->id) }}" class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-primary flex justify-between items-center transition-all group">
                            <div>
                                <div class="font-medium text-gray-800">{{ $category->name_en }}</div>
                                @if($category->name_ku)
                                <div class="text-sm text-gray-500 font-body" dir="rtl">{{ $category->name_ku }}</div>
                                @endif
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($subSection->topics->isNotEmpty())
            <section class="pt-2">
                <h2 class="mb-4 font-heading text-xl font-bold text-gray-700">Topics</h2>
                <div class="grid grid-cols-1 gap-3">
                    @foreach($subSection->topics as $topic)
                        <a href="{{ route('theory.practice', $topic->id) }}" class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-primary flex justify-between items-center transition-all group">
                            <div>
                                <div class="font-medium text-gray-800">{{ $topic->name_en }}</div>
                                @if($topic->name_ku)
                                <div class="text-sm text-gray-500 font-body" dir="rtl">{{ $topic->name_ku }}</div>
                                @endif
                                <div class="mt-1 text-xs text-gray-400">
                                    {{ $topic->questions_count }} {{ Str::plural('question', $topic->questions_count) }}
                                    @if($topic->content_pages_count)
                                        <span class="mx-1">&middot;</span>
                                        {{ $topic->content_pages_count }} learning {{ Str::plural('page', $topic->content_pages_count) }}
                                    @endif
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
