<x-layouts.admin title="Edit Topic">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.topics.index') }}" class="text-gray-500 hover:text-primary transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-xl font-heading font-bold text-gray-800">Edit Topic: {{ $topic->name_en }}</h2>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form action="{{ route('admin.topics.update', $topic) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                @include('admin.topics._parent_fields', [
                    'selectedParentType' => $topic->topicable_type,
                    'selectedParentId' => $topic->topicable_id,
                ])

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Topic Name (English)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $topic->name_en) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Topic Name (Kurdish)</label>
                    <input type="text" name="name_ku" dir="rtl" value="{{ old('name_ku', $topic->name_ku) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.topics.index') }}" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition-colors">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
