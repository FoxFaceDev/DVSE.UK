<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
    <table class="w-full whitespace-nowrap text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 font-medium text-gray-600">
            <tr>
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">English Name</th>
                <th class="px-6 py-3">Kurdish Name</th>
                <th class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($topics as $topic)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-gray-500">#{{ $topic->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $topic->name_en }}</td>
                    <td class="px-6 py-4 text-gray-600" dir="rtl">{{ $topic->name_ku ?? '-' }}</td>
                    <td class="flex items-center justify-end gap-2 whitespace-nowrap px-6 py-4 text-right">
                        <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" class="text-sm font-medium text-primary hover:underline">Add Question</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('admin.content-pages.create', ['topic_id' => $topic->id]) }}" class="text-sm font-medium text-purple-700 hover:underline">Add Page</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('admin.topics.edit', $topic) }}" class="text-sm text-gray-500 transition-colors hover:text-primary">Edit</a>
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST" onsubmit="return confirm('Delete this topic?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-gray-500 transition-colors hover:text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
