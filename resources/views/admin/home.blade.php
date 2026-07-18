<x-layouts.admin title="Dashboard">
    <section class="mb-10">
        <div class="mb-5">
            <h2 class="text-xl font-heading font-bold text-slate-900">User statistics</h2>
            <p class="mt-1 text-sm text-slate-600">Account types and email verification across DVSE.UK.</p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
            <div class="admin-card rounded-xl border bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total accounts</p>
                <p class="mt-2 text-3xl font-heading font-bold text-primary-dark">{{ number_format($userStats['total']) }}</p>
                <p class="mt-1 text-xs text-slate-500">All registered accounts</p>
            </div>
            <div class="admin-card rounded-xl border bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-blue-700">Users</p>
                <p class="mt-2 text-3xl font-heading font-bold text-blue-700">{{ number_format($userStats['users']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Standard accounts</p>
            </div>
            <div class="admin-card rounded-xl border bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-purple-700">Instructors</p>
                <p class="mt-2 text-3xl font-heading font-bold text-purple-700">{{ number_format($userStats['instructors']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Teaching accounts</p>
            </div>
            <div class="admin-card rounded-xl border bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-green-700">Verified</p>
                <p class="mt-2 text-3xl font-heading font-bold text-green-700">{{ number_format($userStats['verified']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $userStats['total'] ? round(($userStats['verified'] / $userStats['total']) * 100) : 0 }}% of accounts</p>
            </div>
            <div class="admin-card rounded-xl border bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Unverified</p>
                <p class="mt-2 text-3xl font-heading font-bold text-amber-700">{{ number_format($userStats['unverified']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Awaiting verification</p>
            </div>
            <div class="admin-card rounded-xl border bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-teal-700">New this month</p>
                <p class="mt-2 text-3xl font-heading font-bold text-teal-700">{{ number_format($userStats['new_this_month']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Since {{ now()->startOfMonth()->format('M j') }}</p>
            </div>
        </div>

        <div class="admin-card mt-5 overflow-hidden rounded-xl border bg-white">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h3 class="font-heading font-bold text-slate-900">Recent accounts</h3>
                    <p class="mt-1 text-xs text-slate-500">The latest registered users and instructors.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Latest {{ $recentUsers->count() }}</span>
            </div>

            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="px-6 py-3">Account</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Email status</th>
                        <th class="px-6 py-3 text-right">Registered</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentUsers as $recentUser)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900">{{ $recentUser->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $recentUser->email }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $recentUser->isInstructor() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $recentUser->isInstructor() ? 'Instructor' : 'User' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($recentUser->hasVerifiedEmail())
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-green-700"><span class="h-2 w-2 rounded-full bg-green-500"></span>Verified</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-amber-700"><span class="h-2 w-2 rounded-full bg-amber-500"></span>Unverified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-slate-600">{{ $recentUser->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-slate-500">No accounts have registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-heading font-bold text-gray-800">Main Sections</h2>
        <button onclick="document.getElementById('createSectionModal').classList.remove('hidden')" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Section
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($sections as $section)
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col hover:-translate-y-1 hover:shadow-md transition-all relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full" style="background-color: {{ $section->color ?? '#3b82f6' }}"></div>
            
            <a href="{{ route('admin.sections.show', $section) }}" class="flex items-center justify-between flex-1">
                <div>
                    <h3 class="font-bold text-gray-800 group-hover:text-primary transition-colors">{{ $section->name }}</h3>
                    <span class="text-sm text-gray-500">{{ $section->sub_sections_count ?? 0 }} Sub-sections</span>
                </div>
                <div class="p-3 rounded-lg" style="background-color: {{ ($section->color ?? '#3b82f6') }}20; color: {{ $section->color ?? '#3b82f6' }}">
                    @if($section->icon_path)
                        <img src="{{ $section->icon_path }}" alt="{{ $section->name }}" class="w-6 h-6 object-contain">
                    @else
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    @endif
                </div>
            </a>

            <!-- Action Buttons -->
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end gap-2">
                <button onclick="document.getElementById('editSectionModal{{ $section->id }}').classList.remove('hidden')" class="text-xs font-medium text-gray-500 hover:text-primary transition-colors flex items-center gap-1 px-2 py-1 bg-gray-50 rounded hover:bg-primary-50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Edit
                </button>
                <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this section? All its sub-sections and categories will be lost.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-medium text-gray-500 hover:text-red-600 transition-colors flex items-center gap-1 px-2 py-1 bg-gray-50 rounded hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Edit Section Modal -->
        <div id="editSectionModal{{ $section->id }}" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Edit Section</h3>
                    <button onclick="document.getElementById('editSectionModal{{ $section->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('admin.sections.update', $section) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Name</label>
                        <input type="text" name="name" value="{{ $section->name }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
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
                        @if($section->icon_path)
                            <div class="mb-2 p-2 border border-gray-100 rounded bg-gray-50 inline-block">
                                <img src="{{ $section->icon_path }}" alt="Current Icon" class="w-10 h-10 object-contain">
                            </div>
                        @endif
                        <input type="file" name="icon" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100">
                        <p class="text-[10px] text-gray-400 mt-1">Leave blank to keep current icon</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('editSectionModal{{ $section->id }}').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Create Section Modal -->
    <div id="createSectionModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Add New Section</h3>
                <button onclick="document.getElementById('createSectionModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.sections.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section Name</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Theme Color (Hex or RGB)</label>
                    <div x-data="{ color: '#3b82f6' }" class="flex items-center gap-3 w-full">
                        <input type="color" x-model="color" class="h-10 w-10 p-1 border border-gray-300 rounded cursor-pointer flex-shrink-0 bg-white">
                        <input type="text" name="color" x-model="color" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" placeholder="e.g. #3b82f6 or rgb(59, 130, 246)">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon Image</label>
                    <input type="file" name="icon" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createSectionModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors">Save Section</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
