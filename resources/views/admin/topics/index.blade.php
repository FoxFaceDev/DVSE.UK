<x-layouts.admin title="Manage Categories">
    <div class="mb-6 flex items-center justify-between">
        <div><h2 class="text-xl font-heading font-bold text-gray-800">All Categories</h2><p class="mt-1 text-sm text-gray-500">Categories are grouped and ordered inside their placement.</p></div>
        <a href="{{ route('admin.topics.create') }}" class="rounded-lg bg-primary px-4 py-2 font-medium text-white hover:bg-primary-dark">Add Category</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="space-y-7">
        @forelse($topics->groupBy(fn ($topic) => $topic->topicable_type.':'.$topic->topicable_id) as $placementTopics)
            @php($parent = $placementTopics->first()->topicable)
            <section>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-heading text-lg font-bold text-gray-800">{{ $parent?->name_en ?? $parent?->name ?? 'Unknown placement' }}</h3>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-primary">{{ class_basename($placementTopics->first()->topicable_type) }}</span>
                </div>
                @include('admin.topics._table', ['topics' => $placementTopics])
            </section>
        @empty
            <div class="rounded-lg border border-gray-200 bg-white px-6 py-10 text-center text-gray-500 shadow-sm">No categories found.</div>
        @endforelse
    </div>
</x-layouts.admin>
