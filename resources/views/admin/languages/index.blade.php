<x-layouts.admin title="Languages">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Available languages</h2>
            <p class="mt-1 text-sm text-slate-500">Manage translation languages shown to admins and learners.</p>
        </div>
        <a href="{{ route('admin.languages.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-dark">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add language
        </a>
    </div>

    @if(session('success'))
        <p class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-green-700">{{ session('success') }}</p>
    @endif

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-[850px] w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-4">Language</th>
                    <th class="px-5 py-4">ISO code</th>
                    <th class="px-5 py-4">Text direction</th>
                    <th class="px-5 py-4">Sort order</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($languages as $language)
                    <tr class="transition hover:bg-slate-50/70">
                        <td class="px-5 py-5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-bold uppercase text-primary">{{ substr($language->code, 0, 2) }}</span>
                                <div>
                                    <p class="font-bold text-slate-900">{{ $language->name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Language #{{ $language->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-5"><span class="rounded-md bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold uppercase text-slate-700">{{ $language->code }}</span></td>
                        <td class="px-5 py-5">
                            <p class="font-medium text-slate-800">{{ $language->direction === 'rtl' ? 'Right to left' : 'Left to right' }}</p>
                            <p class="mt-0.5 text-xs uppercase text-slate-500">{{ $language->direction }}</p>
                        </td>
                        <td class="px-5 py-5 font-semibold text-slate-700">{{ $language->sort_order }}</td>
                        <td class="px-5 py-5">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold {{ $language->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                <span class="h-2 w-2 rounded-full {{ $language->is_active ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                                {{ $language->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.languages.edit', $language) }}" class="rounded-lg border border-primary/30 bg-primary/5 px-3 py-2 text-xs font-bold text-primary hover:bg-primary/10">Edit</a>
                                @if($language->code !== 'en')
                                    <form method="POST" action="{{ route('admin.languages.destroy', $language) }}" onsubmit="return confirm('Delete this language?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">No languages have been created.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
