<x-layouts.app :showBack="true" :backUrl="route('home')" title="{{ $section->name }}">
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="font-heading font-bold text-2xl mb-2" style="color: {{ $section->color ?? '#3b82f6' }}">{{ $section->name }}</h1>
            <p class="text-secondary text-sm">Select a sub-section below to continue.</p>
        </div>

        <div class="space-y-4">
            @forelse($section->subSections as $subSection)
                @if($subSection->name === 'Mock Test Theory')
                    <a href="{{ route('frontend.sub_section', $subSection) }}" class="relative block w-full bg-primary hover:bg-primary-dark text-white p-6 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 group overflow-hidden">
                        
                        <!-- Subtle Background Glow -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-black opacity-10 rounded-full blur-xl -ml-8 -mb-8"></div>
                        
                        <!-- Premium Ribbon -->
                        <div class="absolute top-0 right-6">
                            <div class="bg-white/20 backdrop-blur-sm border border-white/30 text-white text-[10px] font-bold px-3 py-1 rounded-b-lg uppercase tracking-wider shadow-sm">
                                Official Mock Test
                            </div>
                        </div>

                        <div class="flex items-center gap-5 relative z-10 mt-2">
                            <!-- Icon Container -->
                            <div class="flex-shrink-0 w-14 h-14 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30 shadow-inner group-hover:bg-white/30 transition-colors duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1">
                                <h3 class="font-heading font-bold text-xl mb-1 text-white tracking-tight group-hover:translate-x-1 transition-transform">
                                    {{ $subSection->name }}
                                </h3>
                                <p class="text-white/80 text-sm font-medium mb-3">50 Questions • 57 Minutes</p>
                                
                                <!-- Decorative Line -->
                                <div class="w-12 h-1 bg-white/30 rounded-full group-hover:w-full transition-all duration-500"></div>
                            </div>
                            
                            <!-- Arrow -->
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white group-hover:bg-white group-hover:text-primary transition-all duration-300 transform group-hover:translate-x-1">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('frontend.sub_section', $subSection->id) }}" class="flex items-center gap-4 bg-white hover:bg-surface-dim border border-gray-100 p-5 rounded-lg shadow-sm transition-all group">
                        <div class="rounded-md p-3 transition-colors" style="background-color: {{ ($subSection->color ?? $section->color ?? '#3b82f6') }}20; color: {{ $subSection->color ?? $section->color ?? '#3b82f6' }}">
                            @if($subSection->icon_path)
                                <img src="{{ $subSection->icon_path }}" alt="{{ $subSection->name }}" class="w-6 h-6 object-contain">
                            @else
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 font-medium text-gray-800" style="color: {{ $subSection->color ?? '' }}">{{ $subSection->name }}</div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            @empty
                <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-100">
                    No sub-sections available yet. Please check back later.
                </div>
            @endforelse
        </div>
        
        @if($section->topics->isNotEmpty())
            <h3 class="font-heading font-bold text-lg mt-6 mb-3 text-gray-700">Topics</h3>
            <div class="grid grid-cols-1 gap-3">
                @foreach($section->topics as $topic)
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
        @endif
    </div>
</x-layouts.app>
