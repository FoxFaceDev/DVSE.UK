<x-layouts.app title="Learn">
    <div class="page-intro">
        <p class="eyebrow">YOUR LEARNING SPACE</p>
        <h1 class="font-heading text-3xl font-bold mt-2">What will you learn today?</h1>
        <p class="text-secondary text-sm mt-3">Explore your sections and pick a place to begin.</p>
    </div>
    <div class="learning-sections">
        @forelse($sections as $section)
            <section class="learning-section">
                <div class="section-heading">
                    <h2>{{ $section->name }}</h2>
                    <a href="{{ route('frontend.section', $section) }}" class="view-section">View section <x-study-icon type="arrow" /></a>
                </div>
                <div class="mode-grid">
                    @forelse($section->subSections as $subSection)
                        <x-mode-card :href="route('frontend.sub_section', $subSection)" :name="$subSection->name" :description="$subSection->description" :color="$subSection->color ?? $section->color" :image="$subSection->icon_path" :index="$loop->index" />
                    @empty
                        <x-mode-card :href="route('frontend.section', $section)" :name="$section->name" :description="$section->description" :color="$section->color" :image="$section->icon_path" />
                    @endforelse
                </div>
            </section>
        @empty
            <div class="empty-state">No learning sections are available yet. Please check back soon.</div>
        @endforelse
    </div>
</x-layouts.app>
