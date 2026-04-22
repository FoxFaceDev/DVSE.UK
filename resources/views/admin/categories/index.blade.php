<x-layouts.admin title="Categories">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-800">Theory Test Categories</h3>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark transition">Add New Category</a>
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
                    <th class="px-6 py-3">Questions</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-gray-500">#{{ $category->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name_en }}</td>
                    <td class="px-6 py-4 text-gray-600" dir="rtl">{{ $category->name_ku ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-blue-50 text-primary text-xs font-bold rounded-full">{{ $category->questions_count }}</span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.questions.create', ['category_id' => $category->id]) }}" class="text-sm text-primary hover:underline">Add Question</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No categories found. Create one to get started.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
