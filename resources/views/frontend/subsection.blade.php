<x-layouts.app :showBack="true" :backUrl="route('frontend.section', $subSection->section_id)" title="{{ $subSection->name }}">
    <div class="space-y-4">
        @if($whatsappNumber)<a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsappNumber) }}" target="_blank" rel="noopener" aria-label="Chat with DVSE on WhatsApp" class="fixed bottom-5 right-5 z-30 flex h-11 w-11 items-center justify-center rounded-full bg-green-600 text-white shadow-lg transition hover:bg-green-700 hover:scale-105"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L.2 24l6.5-1.7a11.8 11.8 0 0 0 5.4 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4Zm-8.4 18.2c-1.7 0-3.4-.5-4.9-1.3l-.4-.2-3.9 1 1-3.8-.2-.4a9.8 9.8 0 1 1 8.4 4.7Zm5.4-7.3c-.3-.1-1.8-.9-2.1-1-.3-.1-.5-.1-.7.2l-.9 1.1c-.2.3-.5.3-.8.1-2-.9-3.3-1.7-4.6-4-.3-.6.3-.6.9-1.6.1-.2.1-.4 0-.6l-.9-2.2c-.2-.5-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.4-1.2 1.2-1.2 2.9s1.2 3.3 1.4 3.6c.2.2 2.5 3.8 6 5.3 2.2.9 3.1 1 4.2.8.7-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.2-.3-.3-.6-.4Z"/></svg></a>@endif
        @foreach($subSection->mockTests as $mockTest)
            <a href="{{ $mockTest->type === 'hazard' ? route('theory.hazard_mock_info') : route('theory.dynamic_mock_info', $mockTest) }}" class="group relative block overflow-hidden rounded-2xl bg-gradient-to-r from-primary-dark to-primary p-6 text-white shadow-lg transition hover:-translate-y-1">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-100">{{ $mockTest->type === 'hazard' ? 'Hazard perception' : 'English-only theory test' }}</span>
                <h2 class="mt-1 font-heading text-xl font-bold">{{ $mockTest->name }}</h2>
                <p class="mt-2 text-sm text-white/80">{{ $mockTest->description ?: ($mockTest->type === 'theory' ? $mockTest->question_count.' questions · '.$mockTest->duration_minutes.' minutes' : 'Official-style hazard clips') }}</p>
            </a>
        @endforeach
        @if(Str::lower($subSection->name) === 'hazard perception' && $subSection->mockTests->isEmpty())
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
                        <a href="{{ $topic->cgi_content_pages_count ? route('theory.hazard_library', $topic) : route('theory.practice', $topic) }}" class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-primary flex justify-between items-center transition-all group">
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
