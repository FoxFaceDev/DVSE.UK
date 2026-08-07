<x-layouts.admin title="User Accounts">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Registered users</h2>
            <p class="mt-1 text-sm text-slate-500">Contact details, account status and mock-test activity.</p>
        </div>

        <form method="GET" class="flex w-full max-w-xl gap-2">
            <label for="user-search" class="sr-only">Search user accounts</label>
            <div class="relative min-w-0 flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                <input id="user-search" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone or location"
                       class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm outline-none placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <button class="rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-dark">Search</button>
            @if(request('q'))
                <a href="{{ route('admin.users.index') }}" class="flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <p class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-green-700">{{ session('success') }}</p>
    @endif

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-[1280px] w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-4">User</th>
                    <th class="px-5 py-4">Contact</th>
                    <th class="px-5 py-4">Location</th>
                    <th class="px-5 py-4">Account status</th>
                    <th class="px-5 py-4">Test activity</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr class="align-top transition hover:bg-slate-50/70">
                        <td class="px-5 py-5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 font-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <div>
                                    <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">User #{{ $user->id }}</p>
                                    <p class="text-xs text-slate-500">Joined {{ $user->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-5">
                            <a href="mailto:{{ $user->email }}" class="font-medium text-primary hover:underline">{{ $user->email }}</a>
                            <p class="mt-1 text-slate-600">{{ $user->phone_number ?: 'No phone number' }}</p>
                        </td>
                        <td class="max-w-xs px-5 py-5">
                            <p class="font-medium text-slate-800">{{ collect([$user->city, $user->country])->filter()->implode(', ') ?: 'Not provided' }}</p>
                            <p class="mt-1 break-words text-xs leading-5 text-slate-500">{{ $user->address ?: 'No address provided' }}</p>
                        </td>
                        <td class="px-5 py-5">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $user->isInstructor() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">{{ $user->isInstructor() ? 'Instructor' : 'User' }}</span>
                            <div class="mt-2 flex items-center gap-1.5 text-xs {{ $user->hasVerifiedEmail() ? 'text-green-700' : 'text-amber-700' }}">
                                <span class="h-2 w-2 rounded-full {{ $user->hasVerifiedEmail() ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                                {{ $user->hasVerifiedEmail() ? 'Email verified' : 'Email not verified' }}
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Marketing: {{ $user->marketing_email_opt_in ? 'Subscribed' : 'Not subscribed' }}</p>
                        </td>
                        <td class="px-5 py-5">
                            <p class="font-semibold text-slate-800">{{ $user->mock_test_histories_count }} mock tests</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $user->passed_mock_tests_count }} passed</p>
                            <p class="text-xs text-slate-500">Last test: {{ $user->mock_test_histories_max_created_at ? \Illuminate\Support\Carbon::parse($user->mock_test_histories_max_created_at)->format('d M Y') : 'Never' }}</p>
                        </td>
                        <td class="px-5 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg border border-primary/30 bg-primary/5 px-3 py-2 text-xs font-bold text-primary hover:bg-primary/10">Edit / reset password</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">No users matched your search.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-layouts.admin>
