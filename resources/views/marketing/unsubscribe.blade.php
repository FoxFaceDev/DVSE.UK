<x-layouts.app :showBack="false" title="Email preferences">
    <div class="mx-auto max-w-md">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-lg shadow-primary/5">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full {{ $user->marketing_email_opt_in ? 'bg-blue-50 text-primary' : 'bg-green-50 text-green-700' }}">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l9 6 9-6M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/></svg>
            </div>

            @if(session('unsubscribed') || ! $user->marketing_email_opt_in)
                <h1 class="mt-4 font-heading text-2xl font-bold text-primary-dark">You are unsubscribed</h1>
                <p class="mt-2 leading-6 text-gray-600">We will no longer send marketing emails to {{ $user->email }}. Essential account and security emails are not affected.</p>
            @else
                <h1 class="mt-4 font-heading text-2xl font-bold text-primary-dark">Unsubscribe from marketing emails?</h1>
                <p class="mt-2 leading-6 text-gray-600">This will stop DVSE.UK offers, product news and promotional emails to {{ $user->email }}.</p>
                <form method="POST" action="{{ url()->full() }}" class="mt-6">
                    @csrf
                    <button type="submit" class="min-h-12 w-full rounded-xl bg-primary px-5 py-3 font-bold text-white hover:bg-primary-dark">Unsubscribe</button>
                </form>
            @endif

            <a href="{{ route('home') }}" class="mt-5 inline-block text-sm font-semibold text-primary hover:underline">Return to DVSE.UK</a>
        </div>
    </div>
</x-layouts.app>
