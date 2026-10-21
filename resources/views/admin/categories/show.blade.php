<x-layouts.admin title="{{ $category->name_en }} Categories">
    <div class="mb-6 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.sections.sub_sections.show', [$section, $subSection]) }}" class="text-gray-500 hover:text-primary transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-xl font-heading font-bold text-gray-800">{{ $category->name_en }} <span class="text-sm font-normal text-gray-500 ml-2">Categories</span></h2>
        </div>
        <a href="{{ route('admin.topics.create', ['topicable_type' => 'App\Models\Category', 'topicable_id' => $category->id]) }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-md border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if($category->topics->isNotEmpty())
        @include('admin.topics._table', ['topics' => $category->topics])
    @else
        <div class="rounded-lg border border-gray-200 bg-white px-6 py-8 text-center text-gray-500 shadow-sm">No categories found in this group. Create one to get started.</div>
    @endif
</x-layouts.admin>
