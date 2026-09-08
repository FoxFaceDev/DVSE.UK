<x-layouts.admin title="Advertisements">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Manage Advertisements</h3>
            <p class="mt-1 text-sm text-gray-500">Search and manage ads by title, link, language or topic.</p>
        </div>
        <a href="{{ route('admin.ads.create') }}" class="px-4 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create New Ad
        </a>
    </div>

    <form method="GET" class="mb-5 flex w-full max-w-2xl gap-2">
        <label for="ad-search" class="sr-only">Search advertisements</label>
        <div class="relative min-w-0 flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            <input id="ad-search" name="q" value="{{ request('q') }}" placeholder="Search title, link, language or topic"
                   class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm outline-none placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20">
        </div>
        <button class="rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-dark">Search</button>
        @if(request('q'))
            <a href="{{ route('admin.ads.index') }}" class="flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear</a>
        @endif
    </form>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-md border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 p-4 text-amber-800" role="alert">
            {{ session('warning') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <table class="min-w-[1100px] w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-medium">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Title</th>
                    <th class="px-6 py-3">Media</th>
                    <th class="px-6 py-3">Language</th>
                    <th class="px-6 py-3">Target Topics</th>
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
                        <div class="flex flex-wrap gap-1.5">@forelse($ad->languages as $language)<span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">{{ $language->name }}</span>@empty<span class="text-xs text-slate-500">Legacy: all languages</span>@endforelse</div>
                    </td>
                    <td class="max-w-sm px-6 py-4">
                        @if($ad->targets_all_topics)
                            <span class="inline-flex rounded-full bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary">All Topics</span>
                        @else
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($ad->topics as $topic)
                                    <span class="rounded bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $topic->name_en }}</span>
                                @empty
                                    <span class="text-xs font-medium text-red-600">No topics selected</span>
                                @endforelse
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('admin.ads.toggle-status', $ad) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full transition-colors
                                {{ $ad->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                <span class="w-2 h-2 rounded-full {{ $ad->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ ! $ad->is_active ? 'Inactive' : ($ad->starts_at?->isFuture() ? 'Scheduled' : ($ad->expires_at?->isPast() ? 'Expired' : 'Active')) }}
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
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">{{ request('q') ? 'No advertisements matched your search.' : 'No advertisements found. Create your first ad to monetize your platform.' }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $ads->links() }}</div>
</x-layouts.admin>
