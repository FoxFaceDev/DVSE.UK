<x-layouts.app :showBack="true" :backUrl="route('home')" title="My account">
    <div class="space-y-5">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-primary text-white flex items-center justify-center text-2xl font-heading font-bold shadow-sm">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">My account</p>
                <h1 class="text-2xl font-heading font-bold text-primary-dark truncate">{{ $user->name }}</h1>
                <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                <span class="mt-1.5 inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $user->isInstructor() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-primary' }}">
                    {{ $user->isInstructor() ? 'Instructor' : 'User' }}
                </span>
            </div>
        </div>

        @if(!$user->hasVerifiedEmail())
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 9v3m0 4h.01M10.3 4.8 2.9 17.5A2 2 0 0 0 4.6 20h14.8a2 2 0 0 0 1.7-2.5L13.7 4.8a2 2 0 0 0-3.4 0Z"/></svg>
                    <div class="min-w-0">
                        <h2 class="font-bold text-amber-900">Email not verified</h2>
                        <p class="mt-1 text-sm leading-5 text-amber-800">Verify your email to keep your mock-test history connected to your account.</p>
                        <form method="POST" action="{{ route('verification.send') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="min-h-10 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-bold hover:bg-amber-700">Resend email</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="flex items-center gap-2 rounded-xl bg-green-50 border border-green-100 px-4 py-3 text-sm text-green-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>
                <span class="font-semibold">Email verified</span>
            </div>
        @endif

        <div class="grid grid-cols-3 gap-2">
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm"><p class="text-xl font-heading font-bold text-primary">{{ $stats['tests'] }}</p><p class="mt-1 text-[11px] text-gray-500 uppercase tracking-wide">Tests</p></div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm"><p class="text-xl font-heading font-bold text-success">{{ $stats['passes'] }}</p><p class="mt-1 text-[11px] text-gray-500 uppercase tracking-wide">Passed</p></div>
            <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm"><p class="text-xl font-heading font-bold text-primary">{{ $stats['best'] }}%</p><p class="mt-1 text-[11px] text-gray-500 uppercase tracking-wide">Best</p></div>
        </div>

        <section class="rounded-2xl border border-primary/15 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary">Hazard learning</p>
                    <h2 class="mt-1 font-heading text-lg font-bold text-primary-dark">{{ $hazardProgress['completed'] }} of {{ $hazardProgress['total'] }} clips completed</h2>
                    <p class="mt-1 text-xs text-gray-500">Complete more hazard clips to increase your progress.</p>
                </div>
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-primary/10 font-heading text-sm font-bold text-primary">{{ $hazardProgress['percent'] }}%</span>
            </div>
            <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-gradient-to-r from-primary to-sky-400" style="width: {{ $hazardProgress['percent'] }}%"></div></div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-heading font-bold text-primary-dark">Personal details</h2><p class="text-xs text-gray-500 mt-1">Update the name and email used on your account.</p></div>
            <form method="POST" action="{{ route('account.profile.update') }}" class="p-5 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label for="account-name" class="block text-sm font-semibold text-gray-700 mb-1.5">Full name</label>
                    <input id="account-name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="account-email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                    <input id="account-email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false" class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('email') border-red-500 @enderror">
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="account-phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Phone number</label>
                    <input id="account-phone" type="tel" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required autocomplete="tel" inputmode="tel" class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('phone_number') border-red-500 @enderror">
                    @error('phone_number') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                @unless($user->isInstructor())
                    <div><label for="account-language" class="block text-sm font-semibold text-gray-700 mb-1.5">Language to study with English</label><select id="account-language" name="preferred_language_id" required class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('preferred_language_id') border-red-500 @enderror">@foreach($languages as $language)<option value="{{ $language->id }}" @selected((string) old('preferred_language_id', $user->preferred_language_id) === (string) $language->id)>{{ $language->name }}</option>@endforeach</select><p class="mt-1.5 text-xs text-gray-500">This setting controls translations throughout learning sections and topics.</p>@error('preferred_language_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror</div>
                @endunless
                <div>
                    <label for="profile-current-password" class="block text-sm font-semibold text-gray-700 mb-1.5">Current password <span class="font-normal text-gray-500">(only needed when changing email)</span></label>
                    <input id="profile-current-password" type="password" name="current_password" autocomplete="current-password" class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('current_password') border-red-500 @enderror">
                    @error('current_password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full min-h-12 rounded-xl bg-primary text-white font-bold hover:bg-primary-dark transition-colors">Save details</button>
            </form>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-bold text-primary-dark">Marketing emails</h2>
                <p class="mt-1 text-xs text-gray-500">Choose whether DVSE.UK may email you about learning products, services and promotions.</p>
            </div>
            <form method="POST" action="{{ route('account.marketing-preferences.update') }}" class="p-5">
                @csrf @method('PATCH')
                <input type="hidden" name="marketing_email_opt_in" value="0">
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="checkbox" name="marketing_email_opt_in" value="1" @checked($user->marketing_email_opt_in) class="mt-1 h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span>
                        <span class="block text-sm font-semibold text-gray-800">I want to receive marketing emails</span>
                        <span class="mt-1 block text-xs leading-5 text-gray-500">You can change this preference or unsubscribe from any campaign at any time.</span>
                    </span>
                </label>
                <button type="submit" class="mt-4 min-h-11 w-full rounded-xl border border-primary px-4 py-2.5 font-bold text-primary hover:bg-primary/5">Save email preference</button>
            </form>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-heading font-bold text-primary-dark">Password & security</h2><p class="text-xs text-gray-500 mt-1">Change your password regularly to keep your account safe.</p></div>
            <form method="POST" action="{{ route('account.password.update') }}" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label for="current-password" class="block text-sm font-semibold text-gray-700 mb-1.5">Current password</label>
                    <input id="current-password" type="password" name="current_password" required autocomplete="current-password" class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    @error('current_password', 'updatePassword') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="new-password" class="block text-sm font-semibold text-gray-700 mb-1.5">New password</label>
                    <input id="new-password" type="password" name="password" required autocomplete="new-password" class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    <p class="mt-1.5 text-xs text-gray-500">At least 8 characters, including letters and numbers.</p>
                    @error('password', 'updatePassword') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="new-password-confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm new password</label>
                    <input id="new-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                </div>
                <button type="submit" class="w-full min-h-12 rounded-xl border border-primary text-primary font-bold hover:bg-primary/5 transition-colors">Change password</button>
            </form>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3"><div><h2 class="font-heading font-bold text-primary-dark">Recent test history</h2><p class="text-xs text-gray-500 mt-1">Your latest mock-test results.</p></div>@if($user->hasVerifiedEmail())<a href="{{ route('history') }}" class="text-sm font-bold text-primary whitespace-nowrap">View all</a>@endif</div>
            @forelse($recentHistories as $history)
                <div class="px-5 py-4 flex items-center gap-3 border-b border-gray-50 last:border-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center {{ $history->passed ? 'bg-green-50 text-success' : 'bg-red-50 text-error' }}"><span class="text-sm font-bold">{{ $history->passed ? '✓' : '×' }}</span></div>
                    <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-800">Mock test</p><p class="text-xs text-gray-500">{{ $history->created_at->format('M d, Y') }}</p></div>
                    <div class="text-right"><p class="font-heading font-bold {{ $history->passed ? 'text-success' : 'text-error' }}">{{ $history->score }}/{{ $history->total_questions }}</p><p class="text-[10px] uppercase font-bold text-gray-400">{{ $history->passed ? 'Passed' : 'Failed' }}</p></div>
                </div>
            @empty
                <div class="px-5 py-7 text-center text-sm text-gray-500">No mock tests yet. Complete one to see your results here.</div>
            @endforelse
        </section>

        <section class="rounded-2xl border border-red-100 bg-red-50/60 p-5">
            <h2 class="font-heading font-bold text-red-800">Delete account</h2>
            <p class="mt-1 text-sm leading-5 text-red-700">This permanently removes your profile and test history.</p>
            <form method="POST" action="{{ route('account.destroy') }}" class="mt-4 space-y-3" onsubmit="return confirm('Delete your DVSE.UK account and all test history? This cannot be undone.');">
                @csrf @method('DELETE')
                <label for="delete-password" class="block text-sm font-semibold text-red-800">Confirm with your password</label>
                <input id="delete-password" type="password" name="password" required autocomplete="current-password" class="w-full min-h-12 px-4 py-3 rounded-xl bg-white border border-red-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                @error('password', 'deleteAccount') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <button type="submit" class="w-full min-h-12 rounded-xl border border-red-300 text-red-700 font-bold hover:bg-red-100 transition-colors">Delete my account</button>
            </form>
        </section>

        <form method="POST" action="{{ route('logout') }}" class="pb-2">
            @csrf
            <button type="submit" class="w-full min-h-12 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors">Sign out</button>
        </form>
    </div>
</x-layouts.app>
