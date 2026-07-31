<x-layouts.admin title="Questions">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="text-lg font-medium text-gray-800">Bank of Questions</h3>
        
        <div class="flex items-center gap-3 flex-wrap">
            <!-- Topic Filter -->
            <form method="GET" action="{{ route('admin.questions.index') }}" id="filterForm">
                <select name="topic_id" onchange="document.getElementById('filterForm').submit()"
                    class="border-gray-300 rounded-md shadow-sm text-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 min-w-[200px]">
                    <option value="">All Topics</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" @selected((string) request('topic_id') === (string) $topic->id)>
                            {{ $topic->name_en }} ({{ $topic->questions_count }})
                        </option>
                    @endforeach
                </select>
            </form>
            
            <a href="{{ route('admin.questions.create') }}" class="px-4 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark transition whitespace-nowrap">Add New Question</a>
        </div>
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
                    <th class="px-6 py-3">Topic</th>
                    <th class="px-6 py-3">Media</th>
                    <th class="px-6 py-3 w-1/3">Question</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($questions as $question)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">#{{ $question->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="bg-surface-container text-primary px-2 py-1 rounded text-xs">{{ $question->topic?->name_en ?? 'No topic' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($question->media_type)
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full
                                {{ $question->media_type === 'video' ? 'bg-purple-100 text-purple-700' : ($question->media_type === 'gif' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                @if($question->media_type === 'video')
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($question->media_type === 'gif')
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                                @else
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                                {{ ucfirst($question->media_type) }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">None</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900 line-clamp-2">{{ $question->text_en }}</div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap flex justify-end items-center gap-2">
                        <a href="{{ route('admin.questions.edit', $question) }}" class="text-sm text-primary hover:underline font-medium">Edit</a>
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Delete this question?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No questions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
