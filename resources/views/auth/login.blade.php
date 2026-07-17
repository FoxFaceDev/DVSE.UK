<x-layouts.app :showBack="true" :backUrl="route('home')" title="Sign in">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl p-5 sm:p-8 shadow-lg shadow-primary/5 border border-gray-100">
            <div class="text-center mb-7">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m-5-4 5-5-5-5m5 5H3"/></svg>
                </div>
                <h1 class="text-2xl font-heading font-bold text-primary-dark">Welcome back</h1>
                <p class="text-gray-500 mt-2 text-sm">Sign in to save your progress and view your test history.</p>
            </div>

            @if($errors->any())
                <div role="alert" class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
                    Please check your details and try again.
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" x-data="{ loading: false, showPassword: false }" @submit="loading = true">
                @csrf
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false"
                           class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800 @error('email') border-red-500 @enderror"
                           placeholder="you@example.com">
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               class="w-full min-h-12 px-4 py-3 pr-14 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800 @error('password') border-red-500 @enderror"
                               placeholder="Your password">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 -translate-y-1/2 min-w-10 min-h-10 text-xs font-semibold text-primary rounded-lg hover:bg-primary/10" :aria-label="showPassword ? 'Hide password' : 'Show password'" x-text="showPassword ? 'Hide' : 'Show'"></button>
                    </div>
                    @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between gap-3 mb-7">
                    <label class="flex items-center text-sm text-gray-600 cursor-pointer min-h-10">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary mr-2">
                        <span>Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-primary hover:text-primary-dark">Forgot password?</a>
                </div>

                <button type="submit" class="w-full min-h-12 py-3.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-base shadow-md shadow-primary/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2" :disabled="loading">
                    <span x-show="!loading">Sign in</span>
                    <span x-show="loading" class="flex items-center gap-2" style="display: none;">Signing in...</span>
                </button>
            </form>

            <div class="mt-7 pt-6 border-t border-gray-100 text-center text-sm text-gray-500">
                New to DVSE.UK?
                <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary-dark">Create an account</a>
            </div>
        </div>
    </div>
</x-layouts.app>
