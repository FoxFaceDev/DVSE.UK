<x-layouts.admin title="Edit Category">
    <div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.sections.sub_sections.categories.update', [$section, $subSection, $category]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category Name (English) *</label>
                <input type="text" name="name_en" value="{{ $category->name_en }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category Name (Kurdish)</label>
                <input type="text" name="name_ku" value="{{ $category->name_ku }}" dir="rtl" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('admin.sections.sub_sections.show', [$section, $subSection]) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark transition">Save Changes</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
