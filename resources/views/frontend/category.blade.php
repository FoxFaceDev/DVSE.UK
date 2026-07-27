<x-layouts.app :showBack="true" :backUrl="route('frontend.sub_section', $category->sub_section_id)" title="{{ $category->name_en }}">
    <div class="space-y-4">
        <h2 class="font-heading font-bold text-xl mb-4" style="color: {{ $category->subSection->color ?? '#1e293b' }}">{{ $category->name_en }} Topics</h2>
        
        <div class="grid grid-cols-1 gap-3">
            @forelse($category->topics as $topic)
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
            @empty
                <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-100">
                    No topics available in this category yet. Please check back later.
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
