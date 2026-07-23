<x-layouts.admin title="Learning Pages">
    <div class="mb-6 flex items-center justify-between gap-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Learning pages</h3>
            <p class="mt-1 text-sm text-gray-500">Manage CGI clip pages and motorway sign explanations used in mobile practice.</p>
        </div>
        <a href="{{ route('admin.content-pages.create') }}" class="whitespace-nowrap rounded-lg bg-primary px-4 py-2 font-medium text-white transition-colors hover:bg-primary-dark">Add Learning Page</a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.content-pages.index') }}" class="mb-5 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(16rem,1.5fr)_1fr_1fr_1fr_auto]">
        <div class="relative">
            <label for="learning-page-search" class="sr-only">Search learning pages</label>
            <svg class="pointer-events-none absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
            <input id="learning-page-search" name="q" type="search" value="{{ request('q') }}" placeholder="Search title, content, category or ID" class="w-full rounded-lg border-slate-400 bg-white pl-10 text-sm shadow-sm placeholder:text-slate-500 focus:border-primary focus:ring-primary">
        </div>
        <select name="category_id" aria-label="Filter by category" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name_en }}</option>
            @endforeach
        </select>
        <select name="type" aria-label="Filter by page type" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
            <option value="">All page types</option>
            <option value="cgi_clips" @selected(request('type') === 'cgi_clips')>CGI clips</option>
            <option value="motorway_sign" @selected(request('type') === 'motorway_sign')>Motorway sign</option>
        </select>
        <select name="sort" aria-label="Sort learning pages" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest first</option>
            <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
            <option value="title" @selected(request('sort') === 'title')>Title A–Z</option>
        </select>
        <button class="rounded-md bg-gray-800 px-5 py-2 text-sm font-medium text-white hover:bg-gray-900">Search</button>
        </div>
        @if(request()->hasAny(['q', 'category_id', 'type', 'sort']))
            <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-sm">
                <span class="text-gray-500">{{ $contentPages->total() }} {{ Str::plural('page', $contentPages->total()) }} found</span>
                <a href="{{ route('admin.content-pages.index') }}" class="font-medium text-primary hover:underline">Clear search and filters</a>
            </div>
        @endif
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-6 py-3">Page</th>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Preview</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contentPages as $contentPage)
                    <tr class="hover:bg-gray-50/70">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $contentPage->admin_title ?: 'Untitled learning page' }}</div>
                            <div class="mt-1 text-xs text-gray-400">ID #{{ $contentPage->id }} · Updated {{ $contentPage->updated_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $contentPage->category->name_en }}</td>
                        <td class="px-6 py-4">
                            @if($contentPage->type === 'cgi_clips')
                                <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-bold text-purple-700">CGI clips</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">Motorway sign</span>
                            @endif
                        </td>
                        <td class="max-w-md px-6 py-4 text-gray-600">
                            @if($contentPage->type === 'cgi_clips')
                                <div class="flex items-center gap-3">
                                    <span class="flex h-12 w-16 flex-none items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 10 4.553-2.276A1 1 0 0 1 21 8.618v6.764a1 1 0 0 1-1.447.894L15 14M5 18h8a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2Z"/></svg>
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-800">{{ $contentPage->clips->count() }} {{ Str::plural('clip', $contentPage->clips->count()) }}</div>
                                        <div class="mt-1 max-w-xs truncate text-xs text-gray-500">{{ $contentPage->text_en ?: 'No optional text' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-3">
                                    @if($contentPage->sign_image_path)
                                        <img src="{{ $contentPage->sign_image_path }}" alt="" class="h-12 w-16 flex-none rounded-lg border border-gray-200 bg-gray-50 object-contain p-1">
                                    @endif
                                    <div class="max-w-xs line-clamp-2">{{ $contentPage->explanation_en }}</div>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.content-pages.edit', $contentPage) }}" class="font-medium text-primary hover:underline">Edit</a>
                            <span class="mx-2 text-gray-300">|</span>
                            <form action="{{ route('admin.content-pages.destroy', $contentPage) }}" method="POST" class="inline" onsubmit="return confirm('Delete this learning page?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-gray-500 transition-colors hover:text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">No learning pages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contentPages->hasPages())
        <div class="mt-5">{{ $contentPages->links() }}</div>
    @endif
</x-layouts.admin>
