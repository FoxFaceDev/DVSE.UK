<x-layouts.app :showBack="true" :backUrl="route('home')" title="{{ $section->name }}">
    <div class="space-y-6">
        <div class="page-intro">
            <h1 class="font-heading font-bold text-2xl mb-2" style="color: {{ $section->color ?? '#3b82f6' }}">{{ $section->name }}</h1>
            <p class="text-secondary text-sm">Choose your next step. Every session counts.</p>
        </div>

        <div class="space-y-4">
            @forelse($section->subSections as $subSection)
                <x-mode-card :href="route('frontend.sub_section', $subSection->id)" :name="$subSection->name" :description="$subSection->description" :image="$subSection->icon_path" :color="$subSection->color ?? $section->color" :index="$loop->index" />
            @empty
                <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-100">
                    No sub-sections available yet. Please check back later.
                </div>
            @endforelse
        </div>
        
        @if($section->topics->isNotEmpty())
            <h3 class="font-heading font-bold text-lg mt-6 mb-3 text-gray-700">Topics</h3>
            <div class="learning-card-grid">
                @foreach($section->topics as $topic)
                    <x-topic-card :topic="$topic" :color="$section->color" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
