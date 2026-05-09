<x-layouts.admin title="Advertisements">
    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-800">Manage Advertisements</h3>
        <a href="{{ route('admin.ads.create') }}" class="px-4 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create New Ad
        </a>
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
                    <th class="px-6 py-3">Title</th>
                    <th class="px-6 py-3">Media</th>
                    <th class="px-6 py-3">Target Category</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($ads as $ad)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">#{{ $ad->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $ad->title }}</div>
                        <a href="{{ $ad->link_url }}" target="_blank" class="text-xs text-blue-500 hover:underline truncate block max-w-[200px]">{{ $ad->link_url }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full {{ $ad->media_type === 'video' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            @if($ad->media_type === 'video')
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                            @else
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @endif
                            {{ ucfirst($ad->media_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($ad->category)
                            <span class="bg-surface-container text-primary px-2 py-1 rounded text-xs">{{ $ad->category->name_en }}</span>
                        @else
                            <span class="text-xs text-gray-400 italic">All Categories</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('admin.ads.toggle-status', $ad) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full transition-colors
                                {{ $ad->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                <span class="w-2 h-2 rounded-full {{ $ad->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $ad->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap flex justify-end items-center gap-2">
                        <a href="{{ route('admin.ads.edit', $ad) }}" class="text-sm text-primary hover:underline font-medium">Edit</a>
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" onsubmit="return confirm('Delete this advertisement?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No advertisements found. Create your first ad to monetize your platform.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
