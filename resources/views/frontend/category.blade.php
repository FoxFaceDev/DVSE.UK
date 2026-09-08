<x-layouts.app :showBack="true" :backUrl="route('frontend.sub_section', $category->sub_section_id)" title="{{ $category->name_en }}">
    <div class="space-y-4">
        <div class="page-intro">
            <p class="eyebrow">EXPLORE TOPICS</p>
            <h1 class="font-heading text-2xl font-bold mt-2">{{ $category->name_en }}</h1>
            @if($category->name_ku)<p class="text-secondary mt-2" lang="ku" dir="rtl">{{ $category->name_ku }}</p>@endif
            <p class="text-secondary text-sm mt-3">Choose a topic and take your next step.</p>
        </div>
        
        <div class="learning-card-grid">
            @forelse($category->topics as $topic)
                <x-topic-card :topic="$topic" :color="$category->subSection->color ?? $category->subSection->section->color" />
            @empty
                <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-100">
                    No topics available in this category yet. Please check back later.
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
