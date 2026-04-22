<x-layouts.admin title="Questions">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-800">Bank of Questions</h3>
        <a href="{{ route('admin.questions.create') }}" class="px-4 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark transition">Add New Question</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-md border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-medium">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3 w-1/2">Question</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($questions as $question)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">#{{ $question->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap"><span class="bg-surface-container text-primary px-2 py-1 rounded text-xs">{{ $question->category->name_en }}</span></td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900 line-clamp-2">{{ $question->text_en }}</div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                        <a href="#" class="text-sm text-primary hover:underline">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No questions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
