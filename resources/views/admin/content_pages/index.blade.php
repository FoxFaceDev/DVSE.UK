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

    <form method="GET" action="{{ route('admin.content-pages.index') }}" class="mb-5 grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-white p-4 md:grid-cols-[1fr_1fr_auto]">
        <select name="category_id" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name_en }}</option>
            @endforeach
        </select>
        <select name="type" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
            <option value="">All page types</option>
            <option value="cgi_clips" @selected(request('type') === 'cgi_clips')>CGI clips</option>
            <option value="motorway_sign" @selected(request('type') === 'motorway_sign')>Motorway sign</option>
        </select>
        <button class="rounded-md bg-gray-800 px-5 py-2 text-sm font-medium text-white hover:bg-gray-900">Filter</button>
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Content</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contentPages as $contentPage)
                    <tr class="hover:bg-gray-50/70">
                        <td class="px-6 py-4 text-gray-500">#{{ $contentPage->id }}</td>
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
                                <div class="font-medium text-gray-800">{{ $contentPage->clips->count() }} {{ Str::plural('clip', $contentPage->clips->count()) }}</div>
                                <div class="mt-1 truncate text-xs text-gray-500">{{ $contentPage->text_en ?: 'No optional text' }}</div>
                            @else
                                <div class="line-clamp-2">{{ $contentPage->explanation_en }}</div>
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
</x-layouts.admin>
