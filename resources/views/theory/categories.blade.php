<x-layouts.app :showBack="true" title="Categories">
    <div class="space-y-4">
        <h2 class="font-heading font-bold text-xl text-primary-dark mb-4">Select Category</h2>
        <div class="grid grid-cols-1 gap-3">
            @forelse($categories as $category)
                <a href="{{ route('theory.practice', $category->id) }}" class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-primary flex justify-between items-center transition-all group">
                    <div>
                        <div class="font-medium text-gray-800">{{ $category->name_en }}</div>
                        @if($category->name_ku)
                        <div class="text-sm text-gray-500 font-body" dir="rtl">{{ $category->name_ku }}</div>
                        @endif
                        <div class="text-xs text-gray-400 mt-1">{{ $category->questions_count }} Questions</div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @empty
                <div class="p-8 text-center text-gray-500 bg-white rounded-lg border border-gray-100">
                    No categories available yet. Please check back later.
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
