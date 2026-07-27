<x-layouts.admin title="Manage Topics">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-heading font-bold text-gray-800">All Topics</h2>
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
                    <th class="px-6 py-3">Name (EN)</th>
                    <th class="px-6 py-3">Name (KU)</th>
                    <th class="px-6 py-3">Parent Type</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($topics as $topic)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-gray-500">#{{ $topic->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $topic->name_en }}</td>
                    <td class="px-6 py-4 text-gray-600" dir="rtl">{{ $topic->name_ku ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ class_basename($topic->topicable_type) }} #{{ $topic->topicable_id }}</td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2 items-center h-full">
                        <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" class="text-sm text-primary hover:underline font-medium">Add Question</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('admin.content-pages.create', ['topic_id' => $topic->id]) }}" class="text-sm font-medium text-purple-700 hover:underline">Add Page</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('admin.topics.edit', $topic) }}" class="text-sm text-gray-500 hover:text-primary transition-colors">Edit</a>
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST" onsubmit="return confirm('Delete this topic?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No topics found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
