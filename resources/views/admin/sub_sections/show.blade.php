<x-layouts.admin title="{{ $subSection->name }} Categories">
    <div class="mb-6 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.sections.show', $section) }}" class="text-gray-500 hover:text-primary transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-xl font-heading font-bold text-gray-800" style="color: {{ $subSection->color ?? $section->color }}">{{ $subSection->name }}</h2>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.topics.create', ['topicable_type' => 'App\Models\SubSection', 'topicable_id' => $subSection->id]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Topic
            </a>
            <a href="{{ route('admin.sections.sub_sections.categories.create', [$section, $subSection]) }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Category
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-md border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-medium">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">English Name</th>
                    <th class="px-6 py-3">Kurdish Name</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subSection->categories as $category)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-gray-500">#{{ $category->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">
                        <a href="{{ route('admin.sections.sub_sections.categories.show', [$section, $subSection, $category]) }}" class="hover:text-primary hover:underline">
                            {{ $category->name_en }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-gray-600" dir="rtl">{{ $category->name_ku ?? '-' }}</td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2 items-center h-full">
                        <a href="{{ route('admin.topics.create', ['topicable_type' => 'App\Models\Category', 'topicable_id' => $category->id]) }}" class="text-sm font-medium text-primary hover:underline">Add Topic</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('admin.sections.sub_sections.categories.edit', [$section, $subSection, $category]) }}" class="text-sm text-gray-500 hover:text-primary transition-colors">Edit</a>
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('admin.sections.sub_sections.categories.destroy', [$section, $subSection, $category]) }}" method="POST" onsubmit="return confirm('Delete this category?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No categories found in this sub-section. Create one to get started.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($subSection->topics->isNotEmpty())
        <section class="mt-8">
            <h3 class="mb-4 font-heading text-lg font-bold text-gray-800">Topics in {{ $subSection->name }}</h3>
            @include('admin.topics._table', ['topics' => $subSection->topics])
        </section>
    @endif
</x-layouts.admin>
