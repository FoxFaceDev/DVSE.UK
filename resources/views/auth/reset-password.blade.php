<x-layouts.app :showBack="true" :backUrl="route('login')" title="Set a new password">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl p-5 sm:p-8 shadow-lg shadow-primary/5 border border-gray-100">
            <div class="text-center mb-7">
                <h1 class="text-2xl font-heading font-bold text-primary-dark">Set a new password</h1>
                <p class="text-gray-500 mt-2 text-sm">Choose a new password for your DVSE.UK account.</p>
            </div>

            @if($errors->any())
                <div role="alert" class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" x-data="{ showPassword: false, showConfirmation: false }">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false"
                           class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">New password</label>
                    <div class="relative">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password"
                               class="w-full min-h-12 px-4 py-3 pr-14 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 -translate-y-1/2 min-w-10 min-h-10 text-xs font-semibold text-primary rounded-lg hover:bg-primary/10" :aria-label="showPassword ? 'Hide password' : 'Show password'" x-text="showPassword ? 'Hide' : 'Show'"></button>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500">At least 8 characters, including letters and numbers.</p>
                </div>
                <div class="mb-7">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm new password</label>
                    <div class="relative">
                        <input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                               class="w-full min-h-12 px-4 py-3 pr-14 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800">
                        <button type="button" @click="showConfirmation = !showConfirmation" class="absolute right-2 top-1/2 -translate-y-1/2 min-w-10 min-h-10 text-xs font-semibold text-primary rounded-lg hover:bg-primary/10" :aria-label="showConfirmation ? 'Hide password confirmation' : 'Show password confirmation'" x-text="showConfirmation ? 'Hide' : 'Show'"></button>
                    </div>
                </div>
                <button type="submit" class="w-full min-h-12 py-3.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold transition-colors">Reset password</button>
            </form>
        </div>
    </div>
</x-layouts.app>
