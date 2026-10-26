<x-layouts.admin title="Edit Category">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.topics.index') }}" class="text-gray-500 hover:text-primary transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-xl font-heading font-bold text-gray-800">Edit Category: {{ $topic->name_en }}</h2>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form action="{{ route('admin.topics.update', $topic) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="return_to" value="{{ old('return_to', $returnTo) }}">
            
            <div class="space-y-4">
                @include('admin.topics._parent_fields', [
                    'selectedParentType' => $topic->topicable_type,
                    'selectedParentId' => $topic->topicable_id,
                ])

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category Name (English)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $topic->name_en) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category Name (Kurdish)</label>
                    <input type="text" name="name_ku" dir="rtl" value="{{ old('name_ku', $topic->name_ku) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category Content *</label>
                    <select name="content_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                        <option value="questions" @selected(old('content_type', $topic->content_type) === 'questions')>Questions</option>
                        <option value="learning_pages" @selected(old('content_type', $topic->content_type) === 'learning_pages')>Learning pages</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">The type cannot be changed while incompatible content exists.</p>
                    @error('content_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Card Color *</label><input type="color" name="color" value="{{ old('color', $topic->color ?: '#0452bc') }}" required class="h-11 w-full rounded-lg border border-gray-300 p-1"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Card Icon</label>@if($topic->icon_path)<img src="{{ $topic->icon_path }}" alt="" class="mb-2 h-14 w-14 rounded object-contain">@endif<input type="file" name="icon" accept=".svg,image/svg+xml,image/jpeg,image/png,image/webp,image/gif" class="w-full text-sm text-gray-500"></div>
                </div>

                @include('admin.topics._icon_picker', ['topic' => $topic])

                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ $returnTo }}" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition-colors">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
