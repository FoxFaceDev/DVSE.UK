<x-layouts.app :showBack="true" :backUrl="route('frontend.section', $subSection->section_id)" title="{{ $subSection->name }}">
    <div class="space-y-4 browse-page">
        <div class="page-intro"><p class="eyebrow">YOUR NEXT STEP</p><h1 class="font-heading text-2xl font-bold">{{ $subSection->name }}</h1><p class="text-sm text-secondary mt-2">A little learning today. More confidence tomorrow.</p></div>
        @if($subSection->categories->isNotEmpty())
            <section>
                <h2 class="mb-4 font-heading text-xl font-bold" style="color: {{ $subSection->color ?? '#1e293b' }}">Categories</h2>
                <div class="learning-card-grid">
                    @foreach($subSection->categories as $category)
                        <x-learning-card :href="route('frontend.category', $category)" :name="$category->name_en" :translation="$category->name_ku" :color="$subSection->color ?? $subSection->section->color" kind="Category" :meta="$category->topics_count.' '.Str::plural('topic', $category->topics_count)" />
                    @endforeach
                </div>
            </section>
        @endif

        @if($subSection->topics->isNotEmpty())
            <section class="pt-2">
                <h2 class="mb-4 font-heading text-xl font-bold text-gray-700">Topics</h2>
                <div class="learning-card-grid">
                    @foreach($subSection->topics as $topic)
                        <x-topic-card :topic="$topic" :color="$subSection->color ?? $subSection->section->color" />
                    @endforeach
                </div>
            </section>
        @endif

        @if($subSection->mockTests->isNotEmpty() || Str::lower($subSection->name) === 'hazard perception')
            <section class="mock-test-section pt-2">
                <div class="section-heading">
                    <h2>Mock tests</h2>
                    <span>TEST YOUR PROGRESS</span>
                </div>
                <div class="space-y-4">
                    @foreach($subSection->mockTests as $mockTest)
                        <x-mock-test-card :mockTest="$mockTest" />
                    @endforeach
                    @if(Str::lower($subSection->name) === 'hazard perception' && $subSection->mockTests->isEmpty())
                        <x-mock-test-card fallbackHazard />
                    @endif
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
