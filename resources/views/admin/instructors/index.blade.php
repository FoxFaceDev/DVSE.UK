<x-layouts.admin title="Instructors">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Instructor directory</h2>
            <p class="mt-1 text-sm text-slate-500">Add instructors and manage their public profiles.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
            <form method="GET" class="flex gap-2">
                <label for="instructor-search" class="sr-only">Search instructors</label>
                <input id="instructor-search" name="q" value="{{ request('q') }}" placeholder="Search instructors"
                       class="min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 sm:w-72">
                <button class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Search</button>
            </form>
            <a href="{{ route('admin.instructors.create') }}" class="rounded-lg bg-primary px-5 py-2.5 text-center text-sm font-bold text-white shadow-sm hover:bg-primary-dark">+ Add Instructor</a>
        </div>
    </div>

    @if(session('success'))
        <p class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-green-700">{{ session('success') }}</p>
    @endif

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[1050px] text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-4">Instructor</th>
                    <th class="px-5 py-4">Location</th>
                    <th class="px-5 py-4">Lesson details</th>
                    <th class="px-5 py-4">Directory status</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($instructors as $instructor)
                    @php($profile = $instructor->instructorProfile)
                    <tr class="align-top transition hover:bg-slate-50/70">
                        <td class="px-5 py-5">
                            <div class="flex items-center gap-3">
                                @if($profile?->photo_url)
                                    <img src="{{ $profile->photo_url }}" alt="" class="h-11 w-11 rounded-full object-cover">
                                @else
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10 font-bold text-primary">{{ strtoupper(substr($instructor->name, 0, 1)) }}</span>
                                @endif
                                <div>
                                    <p class="font-bold text-slate-900">{{ $instructor->name }}</p>
                                    <a href="mailto:{{ $instructor->email }}" class="text-xs text-primary hover:underline">{{ $instructor->email }}</a>
                                    <p class="text-xs text-slate-500">{{ $instructor->phone_number ?: 'No phone number' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-5">
                            <p class="font-medium text-slate-800">{{ collect([$instructor->city, $instructor->country])->filter()->implode(', ') ?: 'Not provided' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $profile?->postcode ?: 'No profile postcode' }}</p>
                        </td>
                        <td class="px-5 py-5">
                            @if($profile)
                                <p class="font-semibold text-slate-800">£{{ number_format((float) $profile->hourly_rate, 2) }} / hour</p>
                                <p class="mt-1 text-xs capitalize text-slate-500">{{ collect($profile->gearboxes)->implode(', ') }}</p>
                                <p class="text-xs text-slate-500">Rating: {{ $profile->rating ? number_format((float) $profile->rating, 1).' / 5' : 'Not rated' }}</p>
                            @else
                                <span class="text-amber-700">Profile incomplete</span>
                            @endif
                        </td>
                        <td class="px-5 py-5">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $profile?->is_published ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">{{ $profile?->is_published ? 'Published' : 'Unpublished' }}</span>
                                @if($profile?->is_approved)<span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">Approved</span>@endif
                                @if($profile?->is_premium)<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">Premium</span>@endif
                            </div>
                            <p class="mt-2 text-xs {{ $instructor->hasVerifiedEmail() ? 'text-green-700' : 'text-amber-700' }}">{{ $instructor->hasVerifiedEmail() ? 'Email verified' : 'Email not verified' }}</p>
                        </td>
                        <td class="px-5 py-5 text-right">
                            <a href="{{ route('admin.instructors.edit', $instructor) }}" class="inline-flex rounded-lg border border-primary/30 bg-primary/5 px-4 py-2 text-xs font-bold text-primary hover:bg-primary/10">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">No instructors matched your search.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $instructors->links() }}</div>
</x-layouts.admin>
