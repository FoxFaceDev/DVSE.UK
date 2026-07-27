<x-layouts.admin title="{{ $section->name }} Sub-sections">
    <div class="mb-6 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.home') }}" class="text-gray-500 hover:text-primary transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-xl font-heading font-bold text-gray-800" style="color: {{ $section->color }}">{{ $section->name }}</h2>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.topics.create', ['topicable_type' => 'App\Models\Section', 'topicable_id' => $section->id]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Topic
            </a>
            <button onclick="document.getElementById('createSubSectionModal').classList.remove('hidden')" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Sub-section
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($section->subSections as $subSection)
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col hover:-translate-y-1 hover:shadow-md transition-all relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full" style="background-color: {{ $subSection->color ?? $section->color }}"></div>
            
            <a href="{{ route('admin.sections.sub_sections.show', [$section, $subSection]) }}" class="flex items-center justify-between flex-1">
                <div>
                    <h3 class="font-bold text-gray-800 group-hover:text-primary transition-colors">{{ $subSection->name }}</h3>
                    <span class="text-sm text-gray-500">Manage categories</span>
                </div>
                <div class="p-3 rounded-lg" style="background-color: {{ ($subSection->color ?? $section->color) }}20; color: {{ $subSection->color ?? $section->color }}">
                    @if($subSection->icon_path)
                        <img src="{{ $subSection->icon_path }}" alt="{{ $subSection->name }}" class="w-6 h-6 object-contain">
                    @else
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    @endif
                </div>
            </a>

            <!-- Action Buttons -->
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end gap-2">
                <button onclick="document.getElementById('editSubSectionModal{{ $subSection->id }}').classList.remove('hidden')" class="text-xs font-medium text-gray-500 hover:text-primary transition-colors flex items-center gap-1 px-2 py-1 bg-gray-50 rounded hover:bg-primary-50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Edit
                </button>
                <form action="{{ route('admin.sections.sub_sections.destroy', [$section, $subSection]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sub-section? All its categories will be lost.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-medium text-gray-500 hover:text-red-600 transition-colors flex items-center gap-1 px-2 py-1 bg-gray-50 rounded hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Edit SubSection Modal -->
        <div id="editSubSectionModal{{ $subSection->id }}" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Edit Sub-section</h3>
                    <button onclick="document.getElementById('editSubSectionModal{{ $subSection->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('admin.sections.sub_sections.update', [$section, $subSection]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sub-section Name</label>
                        <input type="text" name="name" value="{{ $subSection->name }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Theme Color (Hex or RGB)</label>
                        <div x-data="{ color: '{{ $subSection->color ?? $section->color ?? '#3b82f6' }}' }" class="flex items-center gap-3 w-full">
                            <input type="color" x-model="color" class="h-10 w-10 p-1 border border-gray-300 rounded cursor-pointer flex-shrink-0 bg-white">
                            <input type="text" name="color" x-model="color" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" placeholder="e.g. #3b82f6 or rgb(59, 130, 246)">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon Image</label>
                        @if($subSection->icon_path)
                            <div class="mb-2 p-2 border border-gray-100 rounded bg-gray-50 inline-block">
                                <img src="{{ $subSection->icon_path }}" alt="Current Icon" class="w-10 h-10 object-contain">
                            </div>
                        @endif
                        <input type="file" name="icon" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100">
                        <p class="text-[10px] text-gray-400 mt-1">Leave blank to keep current icon</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('editSubSectionModal{{ $subSection->id }}').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Create SubSection Modal -->
    <div id="createSubSectionModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Add Sub-section to {{ $section->name }}</h3>
                <button onclick="document.getElementById('createSubSectionModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.sections.sub_sections.store', $section) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sub-section Name</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Theme Color (Hex or RGB)</label>
                    <div x-data="{ color: '{{ $section->color ?? '#3b82f6' }}' }" class="flex items-center gap-3 w-full">
                        <input type="color" x-model="color" class="h-10 w-10 p-1 border border-gray-300 rounded cursor-pointer flex-shrink-0 bg-white">
                        <input type="text" name="color" x-model="color" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" placeholder="e.g. #3b82f6 or rgb(59, 130, 246)">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon Image</label>
                    <input type="file" name="icon" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createSubSectionModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
