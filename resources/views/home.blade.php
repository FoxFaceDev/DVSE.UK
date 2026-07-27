<x-layouts.app :showBack="false">
    <div class="space-y-4">
        <!-- Banner/Promo Area (optional representation of space) -->
        <div class="bg-primary text-white rounded-xl p-6 shadow-md shadow-primary/20 relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="font-heading font-bold text-2xl mb-2">Ready to start?</h2>
                <p class="text-sm opacity-90">Select a mode below to begin.</p>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        </div>

        <a href="{{ route('theory.hazard_mock_info') }}" class="group flex items-center gap-4 rounded-lg border border-purple-100 bg-white p-5 shadow-sm transition-all hover:bg-purple-50">
            <div class="rounded-md bg-purple-100 p-3 text-purple-800 transition-colors group-hover:bg-purple-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.9 2.7 17a2 2 0 0 0 1.73 3h15.14a2 2 0 0 0 1.73-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-heading font-bold text-purple-950">Hazard perception mock test</p>
                <p class="mt-1 text-xs text-gray-500">14 clips · 15 hazards · pass mark 44/75</p>
            </div>
            <svg class="h-5 w-5 text-purple-400 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
            </svg>
        </a>

        <!-- Stacked Action Buttons (Dynamic) -->
        @forelse($sections as $section)
        <a href="{{ route('frontend.section', $section->id) }}" class="flex items-center gap-4 bg-white hover:bg-surface-dim border border-gray-100 p-5 rounded-lg shadow-sm transition-all group">
            <div class="rounded-md p-3 transition-colors" style="background-color: {{ ($section->color ?? '#3b82f6') }}20; color: {{ $section->color ?? '#3b82f6' }}">
                @if($section->icon_path)
                    <img src="{{ $section->icon_path }}" alt="{{ $section->name }}" class="w-6 h-6 object-contain">
                @else
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                @endif
            </div>
            <div class="flex-1 font-medium text-gray-800" style="color: {{ $section->color ?? '' }}">{{ $section->name }}</div>
            <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @empty
            <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-100">
                No sections available. Please check back later.
            </div>
        @endforelse
    </div>
</x-layouts.app>
