<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DVSE.UK - {{ $title ?? 'Home' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="bg-surface text-on-surface antialiased min-h-screen flex flex-col font-body">

    <!-- Header & Sidebar Component Area -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-200">
        <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if(isset($showBack) && $showBack)
                <a href="{{ $backUrl ?? url()->previous() }}" class="p-2 -ml-2 text-primary">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                @endif
                <a href="{{ route('home') }}" class="flex items-center gap-2" aria-label="DVSE.UK home">
                    <div class="bg-primary-dark px-2 py-1 rounded-lg flex items-center justify-center" style="height: 36px;">
                        <img src="{{ asset('images/logo.png') }}" alt="DVSE.UK Logo" style="height: 28px; max-height: 28px; width: auto; object-fit: contain;">
                    </div>
                    <span class="font-heading font-bold text-xl text-primary-dark tracking-wide">DVSE.UK</span>
                </a>
            </div>
            
            <div>
                @auth('web')
                    <a href="{{ route('account.show') }}" class="hidden sm:flex items-center gap-2 mr-2 px-3 py-2 rounded-lg text-sm font-semibold text-primary hover:bg-surface-dim" aria-label="Open your account">
                        <span class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center text-xs">{{ strtoupper(substr(auth('web')->user()->name, 0, 1)) }}</span>
                        <span class="max-w-24 truncate">{{ auth('web')->user()->name }}</span>
                    </a>
                @endauth
                <button @click="sidebarOpen = !sidebarOpen" :aria-expanded="sidebarOpen.toString()" aria-controls="mobile-menu" aria-label="Open menu" class="p-2 text-primary focus:outline-none rounded-lg hover:bg-surface-dim">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Sidebar Backdrop & Drawer (placed at body root to break out of sticky header's backdrop-filter context) -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-50" style="display: none;"></div>
    <div id="mobile-menu" x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         @click.away="sidebarOpen = false" 
         class="fixed right-0 top-0 bottom-0 w-64 bg-white shadow-xl z-50 flex flex-col"
         style="display: none;">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <span class="font-bold text-primary">Menu</span>
            <button @click="sidebarOpen = false"><svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <nav class="flex-1 py-4 flex flex-col font-medium text-secondary overflow-y-auto">
            @guest('web')
                <a href="{{ route('login') }}" class="px-6 py-3 min-h-12 flex items-center hover:bg-surface-dim hover:text-primary transition-colors">Sign in</a>
                <a href="{{ route('register') }}" class="mx-4 mt-2 px-4 py-3 min-h-12 flex items-center justify-center bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">Create an account</a>
            @else
                <a href="{{ route('account.show') }}" class="px-6 py-4 mb-2 bg-primary/5 border-b border-primary/10 hover:bg-primary/10">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold">{{ strtoupper(substr(auth('web')->user()->name, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Your account</p>
                            <p class="font-bold text-primary truncate">{{ auth('web')->user()->name }}</p>
                        </div>
                    </div>
                </a>
                @if(!auth('web')->user()->hasVerifiedEmail())
                    <a href="{{ route('verification.notice') }}" class="px-6 py-3 min-h-12 flex items-center gap-2 text-amber-700 bg-amber-50 hover:bg-amber-100 transition-colors">Verify your email</a>
                @endif
                <a href="{{ route('account.show') }}" class="px-6 py-3 min-h-12 flex items-center hover:bg-surface-dim hover:text-primary transition-colors">Account settings</a>
                @if(auth('web')->user()->hasVerifiedEmail())
                    <a href="{{ route('history') }}" class="px-6 py-3 min-h-12 flex items-center hover:bg-surface-dim hover:text-primary transition-colors">Test history</a>
                @endif
            @endguest
            
            <hr class="my-2 border-gray-100">
            <a href="mailto:support@dvse.uk" class="px-6 py-3 min-h-12 flex items-center hover:bg-surface-dim hover:text-primary transition-colors">Contact support</a>

            @auth('web')
            <div class="mt-auto px-6 py-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full min-h-12 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-semibold transition-colors">
                        Sign out
                    </button>
                </form>
            </div>
            @endauth
        </nav>
    </div>

    <!-- Main Content -->
    <main class="flex-1 w-full max-w-md mx-auto px-4 py-6">
        @if(session('status'))
            <div role="status" class="mb-5 rounded-xl border border-primary/15 bg-primary/5 px-4 py-3 text-sm text-primary-dark">
                @switch(session('status'))
                    @case('verification-link-sent') A fresh verification link has been sent to your email. @break
                    @case('email-verified') Your email is verified. Welcome to DVSE.UK! @break
                    @case('profile-updated') Your account details were updated. @break
                    @case('profile-updated-verification-sent') Your details were updated. Please verify your new email address. @break
                    @case('password-updated') Your password was changed successfully. @break
                    @case('account-deleted') Your account has been deleted. @break
                    @default {{ session('status') }}
                @endswitch
            </div>
        @endif
        {{ $slot }}
    </main>

    <!-- Footer Component Area -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-8">
        <div class="max-w-md mx-auto px-4 text-center flex flex-col items-center">
            <div class="bg-primary-dark px-3 py-1.5 rounded-xl flex items-center justify-center mb-3" style="height: 48px;">
                <img src="{{ asset('images/logo.png') }}" alt="DVSE.UK Logo" style="height: 36px; max-height: 36px; width: auto; object-fit: contain;">
            </div>
            <h3 class="font-heading font-bold text-lg text-primary-dark mb-4">DVSE.UK</h3>
            <div class="flex justify-center gap-6 text-sm text-secondary mb-4">
                <a href="mailto:support@dvse.uk" class="hover:text-primary transition-colors">Contact support</a>
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Back to home</a>
            </div>
            <p class="text-xs text-gray-400 flex items-center justify-center gap-1">
                <span>&copy; DVSE.UK</span>
            </p>
        </div>
    </footer>
</body>
</html>
