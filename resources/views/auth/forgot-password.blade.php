<x-layouts.app :showBack="true" :backUrl="route('login')" title="Forgot password">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl p-5 sm:p-8 shadow-lg shadow-primary/5 border border-gray-100">
            <div class="text-center mb-7">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2Zm2-7V8a4 4 0 1 1 8 0v4"/></svg>
                </div>
                <h1 class="text-2xl font-heading font-bold text-primary-dark">Reset your password</h1>
                <p class="text-gray-500 mt-2 text-sm leading-6">Enter your email and we’ll send you a secure reset link.</p>
            </div>

            @if(session('status'))
                <div role="status" class="mb-5 rounded-xl bg-green-50 border border-green-100 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div role="alert" class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false"
                       class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800"
                       placeholder="you@example.com">
                <button type="submit" class="w-full min-h-12 mt-6 py-3.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold transition-colors">Email reset link</button>
            </form>

            <a href="{{ route('login') }}" class="block mt-6 text-center text-sm font-semibold text-primary hover:text-primary-dark">Back to sign in</a>
        </div>
    </div>
</x-layouts.app>
