<x-layouts.app :showBack="true" title="{{ $section->name }}">
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="font-heading font-bold text-2xl mb-2" style="color: {{ $section->color ?? '#3b82f6' }}">{{ $section->name }}</h1>
            <p class="text-secondary text-sm">Select a sub-section below to continue.</p>
        </div>

        @if($section->name === 'Theory Test Practice')
            <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm">
                <label class="block text-sm font-medium text-gray-700 mb-2">Choose Language</label>
                <select class="w-full border-gray-200 rounded-md focus:ring-primary focus:border-primary p-2 bg-surface-dim appearance-none">
                    <option value="en">English</option>
                    <option value="en-ku">English & Kurdish</option>
                </select>
            </div>
        @endif

        <div class="space-y-4">
            @forelse($section->subSections as $subSection)
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
            @empty
                <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-100">
                    No sub-sections available yet. Please check back later.
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
