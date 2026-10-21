<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-gray-100 bg-blue-50/60 px-5 py-2 text-xs text-gray-600">
        <span>Drag categories by the handle to change their display order.</span>
        <span data-sort-status aria-live="polite"></span>
    </div>
    <table class="w-full whitespace-nowrap text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 font-medium text-gray-600">
            <tr>
                <th class="w-16 px-4 py-3">Order</th>
                <th class="px-4 py-3">ID</th>
                <th class="px-6 py-3">English Name</th>
                <th class="px-6 py-3">Kurdish Name</th>
                <th class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100" data-category-sort data-reorder-url="{{ route('admin.topics.reorder') }}">
            @foreach($topics as $topic)
                <tr data-category-id="{{ $topic->id }}" class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-4"><button type="button" draggable="true" data-drag-handle class="cursor-grab rounded p-2 text-gray-400 hover:bg-blue-50 hover:text-primary active:cursor-grabbing" aria-label="Drag {{ $topic->name_en }} to reorder" title="Drag to reorder. Alt + arrow keys also move this category."><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="6" cy="5" r="1.4"/><circle cx="14" cy="5" r="1.4"/><circle cx="6" cy="10" r="1.4"/><circle cx="14" cy="10" r="1.4"/><circle cx="6" cy="15" r="1.4"/><circle cx="14" cy="15" r="1.4"/></svg></button></td>
                    <td class="px-4 py-4 text-gray-500">#{{ $topic->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900"><span class="flex items-center gap-3">@if($topic->icon_path)<img src="{{ $topic->icon_path }}" alt="" class="h-10 w-10 object-contain">@endif{{ $topic->name_en }}</span></td>
                    <td class="px-6 py-4 text-gray-600" dir="rtl">{{ $topic->name_ku ?? '-' }}</td>
                    <td class="flex items-center justify-end gap-2 whitespace-nowrap px-6 py-4 text-right">
                        @if($topic->isQuestionCategory())
                            <a href="{{ route('admin.questions.create', ['category_id' => $topic->id]) }}" class="text-sm font-medium text-primary hover:underline">Add Question</a>
                        @else
                            <a href="{{ route('admin.content-pages.create', ['category_id' => $topic->id]) }}" class="text-sm font-medium text-purple-700 hover:underline">Add Learning Page</a>
                        @endif
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('admin.topics.edit', $topic) }}" class="text-sm text-gray-500 transition-colors hover:text-primary">Edit</a>
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST" onsubmit="return confirm('Delete this category and all of its content?');" class="inline">
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
