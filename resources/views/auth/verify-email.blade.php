<x-layouts.app :showBack="false" title="Verify your email">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl p-5 sm:p-8 shadow-lg shadow-primary/5 border border-gray-100 text-center">
            <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-5 border border-amber-100">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 8.5 12 14l9-5.5M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/></svg>
            </div>
            <h1 class="text-2xl font-heading font-bold text-primary-dark">Check your inbox</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600">We sent a verification link to <span class="font-semibold text-gray-800">{{ auth('web')->user()->email }}</span>. Confirm your email to unlock your test history.</p>

            @if(session('status') === 'verification-link-sent')
                <p role="status" class="mt-4 rounded-xl bg-green-50 border border-green-100 px-4 py-3 text-sm text-green-700">A new verification link is on its way.</p>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-7">
                @csrf
                <button type="submit" class="w-full min-h-12 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold transition-colors">Resend verification email</button>
            </form>

            <a href="{{ route('account.show') }}" class="block mt-4 min-h-12 py-3 text-primary font-semibold hover:text-primary-dark">Go to my account</a>

            <div class="mt-5 pt-5 border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-red-600 font-semibold">Sign out</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
