<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#102b46">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rivex.uk - {{ $title ?? 'Home' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lexend:wght@400;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body x-data="{ sidebarOpen: false, selectedMenuItem: null }" @keydown.escape.window="sidebarOpen = false" class="learner-shell bg-surface text-on-surface antialiased min-h-screen flex flex-col font-body">

    <a href="#main-content" class="skip-link">Skip to content</a>
    <!-- Header & Sidebar Component Area -->
    <header class="app-header bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-200">
        <div class="app-header-inner max-w-md mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if(isset($showBack) && $showBack)
                <a href="{{ $backUrl ?? url()->previous() }}" aria-label="Go back" class="p-2 -ml-2 text-primary">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                @endif
                <a href="{{ route('home') }}" class="flex items-center gap-2" aria-label="Rivex.uk home">
                    <div class="bg-primary-dark px-2 py-1 rounded-lg flex items-center justify-center" style="height: 36px;">
                        <img src="{{ asset('images/rivex/logo.png') }}" alt="Rivex.uk Logo" style="height: 28px; max-height: 28px; width: auto; object-fit: contain;">
                    </div>
                    <span class="font-heading font-bold text-xl text-primary-dark tracking-wide">Rivex.uk<small class="brand-tagline">Drive your future.</small></span>
                </a>
            </div>
            
            <div class="flex items-center">
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
            <button @click="sidebarOpen = false" aria-label="Close menu" class="p-2"><svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <nav class="flex-1 py-4 flex flex-col font-medium text-secondary overflow-y-auto">
            @guest('web')
                <a href="{{ route('login') }}" @click="selectedMenuItem = 'login'" @if(request()->routeIs('login')) aria-current="page" @endif class="mx-3 flex min-h-12 items-center rounded-lg border-l-4 px-4 py-3 transition active:scale-[.98] {{ request()->routeIs('login') ? 'border-primary bg-primary/10 font-bold text-primary' : 'border-transparent hover:bg-surface-dim hover:text-primary' }}" :class="selectedMenuItem === 'login' && 'border-primary bg-primary/10 text-primary'">Sign in</a>
                <a href="{{ route('register') }}" @click="selectedMenuItem = 'register'" @if(request()->routeIs('register')) aria-current="page" @endif class="mx-4 mt-2 flex min-h-12 items-center justify-center rounded-lg border-2 px-4 py-3 transition active:scale-[.98] {{ request()->routeIs('register') ? 'border-primary-dark bg-primary-dark text-white ring-4 ring-primary/15' : 'border-primary bg-primary text-white hover:bg-primary-dark' }}" :class="selectedMenuItem === 'register' && 'ring-4 ring-primary/20'">Create an account</a>
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
                <a href="{{ route('account.show') }}" @click="selectedMenuItem = 'account'" @if(request()->routeIs('account.*')) aria-current="page" @endif class="mx-3 flex min-h-12 items-center rounded-lg border-l-4 px-4 py-3 transition active:scale-[.98] {{ request()->routeIs('account.*') ? 'border-primary bg-primary/10 font-bold text-primary' : 'border-transparent hover:bg-surface-dim hover:text-primary' }}" :class="selectedMenuItem === 'account' && 'border-primary bg-primary/10 text-primary'">Account settings</a>
                @if(auth('web')->user()->hasVerifiedEmail())
                    <a href="{{ route('history') }}" @click="selectedMenuItem = 'history'" @if(request()->routeIs('history')) aria-current="page" @endif class="mx-3 flex min-h-12 items-center rounded-lg border-l-4 px-4 py-3 transition active:scale-[.98] {{ request()->routeIs('history') ? 'border-primary bg-primary/10 font-bold text-primary' : 'border-transparent hover:bg-surface-dim hover:text-primary' }}" :class="selectedMenuItem === 'history' && 'border-primary bg-primary/10 text-primary'">Test history</a>
                @endif
            @endguest
            
            <hr class="my-2 border-gray-100">
            <a href="{{ url('/about-us') }}" @click="selectedMenuItem = 'about'" @if(request()->routeIs('about')) aria-current="page" @endif class="mx-3 flex min-h-12 items-center rounded-lg border-l-4 px-4 py-3 transition active:scale-[.98] {{ request()->routeIs('about') ? 'border-primary bg-primary/10 font-bold text-primary' : 'border-transparent hover:bg-surface-dim hover:text-primary' }}" :class="selectedMenuItem === 'about' && 'border-primary bg-primary/10 text-primary'">About us</a>
            <a href="{{ url('/contact-us') }}" @click="selectedMenuItem = 'contact'" @if(request()->routeIs('contact')) aria-current="page" @endif class="mx-3 flex min-h-12 items-center rounded-lg border-l-4 px-4 py-3 transition active:scale-[.98] {{ request()->routeIs('contact') ? 'border-primary bg-primary/10 font-bold text-primary' : 'border-transparent hover:bg-surface-dim hover:text-primary' }}" :class="selectedMenuItem === 'contact' && 'border-primary bg-primary/10 text-primary'">Contact us</a>

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
    <main id="main-content" class="app-main flex-1 w-full max-w-md mx-auto px-4 py-6">
        @if(session('status'))
            <div role="status" class="mb-5 rounded-xl border border-primary/15 bg-primary/5 px-4 py-3 text-sm text-primary-dark">
                @switch(session('status'))
                    @case('verification-link-sent') A fresh verification link has been sent to your email. @break
                    @case('email-verified') Your email is verified. Welcome to Rivex.uk! @break
                    @case('profile-updated') Your account details were updated. @break
                    @case('profile-updated-verification-sent') Your details were updated. Please verify your new email address. @break
                    @case('password-updated') Your password was changed successfully. @break
                    @case('marketing-subscribed') You are now subscribed to Rivex.uk marketing emails. @break
                    @case('marketing-unsubscribed') You have unsubscribed from Rivex.uk marketing emails. @break
                    @case('account-deleted') Your account has been deleted. @break
                    @default {{ session('status') }}
                @endswitch
            </div>
        @endif
        @if($siteAd ?? null)
            <aside class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg" aria-label="Sponsored advertisement">
                <a href="{{ $siteAd->link_url }}" target="_blank" rel="noopener sponsored" class="block">
                    <div class="relative bg-slate-950">
                        @if($siteAd->media_type === 'video')
                            <video src="{{ $siteAd->media_source }}" autoplay muted loop playsinline class="max-h-64 w-full object-cover"></video>
                        @elseif($siteAd->media_source)
                            <img src="{{ $siteAd->media_source }}" alt="{{ $siteAd->title }}" class="max-h-64 w-full object-cover">
                        @endif
                        <span class="absolute right-2 top-2 rounded-full bg-black/65 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-white">Sponsored</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 p-4"><div><p class="font-bold text-slate-900">{{ $siteAd->title }}</p><p class="mt-1 text-xs text-slate-500">Visit advertiser website</p></div><span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-xl text-white">&rarr;</span></div>
                </a>
            </aside>
        @endif
        {{ $slot }}
    </main>

    @if($showWhatsappButton ?? false)
        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsappNumber) }}" target="_blank" rel="noopener" aria-label="Chat with Rivex.uk on WhatsApp" data-legacy-label="Chat with DVSE on WhatsApp" class="whatsapp-button fixed bottom-5 right-5 z-30 flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white shadow-lg transition hover:scale-105 hover:bg-green-700">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L.2 24l6.5-1.7a11.8 11.8 0 0 0 5.4 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4Zm-8.4 18.2c-1.7 0-3.4-.5-4.9-1.3l-.4-.2-3.9 1 1-3.8-.2-.4a9.8 9.8 0 1 1 8.4 4.7Zm5.4-7.3c-.3-.1-1.8-.9-2.1-1-.3-.1-.5-.1-.7.2l-.9 1.1c-.2.3-.5.3-.8.1-2-.9-3.3-1.7-4.6-4-.3-.6.3-.6.9-1.6.1-.2.1-.4 0-.6l-.9-2.2c-.2-.5-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.4-1.2 1.2-1.2 2.9s1.2 3.3 1.4 3.6c.2.2 2.5 3.8 6 5.3 2.2.9 3.1 1 4.2.8.7-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.2-.3-.3-.6-.4Z"/></svg>
        </a>
    @endif

    <!-- Footer Component Area -->
    <footer class="app-footer" aria-label="Site footer">
        <div class="navbar-container">
            <div class="neon-navbar">
                <div class="dots-pattern dots-left" aria-hidden="true"></div>
                <div class="dots-pattern dots-right" aria-hidden="true"></div>

                <nav class="nav-content" aria-label="Footer navigation">
                    <a href="{{ route('about') }}" class="nav-item">
                        <span class="icon-wrapper" aria-hidden="true">
                            <svg class="ring-svg" viewBox="0 0 74 74">
                                <defs>
                                    <filter id="footerNeonBlueGlow" x="-30%" y="-30%" width="160%" height="160%">
                                        <feGaussianBlur stdDeviation="2.5" result="blur" />
                                        <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
                                    </filter>
                                    <filter id="footerNeonGoldGlow" x="-40%" y="-40%" width="180%" height="180%">
                                        <feGaussianBlur stdDeviation="2.5" result="blur" />
                                        <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
                                    </filter>
                                    <linearGradient id="footerGoldArcGrad" x1="0%" y1="0%" x2="100%" y2="80%">
                                        <stop offset="0%" stop-color="#ffae00" />
                                        <stop offset="50%" stop-color="#ffe600" />
                                        <stop offset="100%" stop-color="#ff7b00" stop-opacity="0.1" />
                                    </linearGradient>
                                </defs>
                                <circle cx="37" cy="37" r="34" fill="none" stroke="#0077ff" stroke-width="2" filter="url(#footerNeonBlueGlow)" />
                                <ellipse cx="58" cy="18" rx="10" ry="6" transform="rotate(25 58 18)" fill="#ffaa00" opacity="0.5" filter="blur(5px)" />
                                <path d="M 37,3 A 34,34 0 0,1 70,30" fill="none" stroke="url(#footerGoldArcGrad)" stroke-width="2.4" stroke-linecap="round" filter="url(#footerNeonGoldGlow)" />
                            </svg>
                            <svg class="inner-icon" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <span class="nav-label-box"><span class="nav-label">About Us</span><span class="nav-underline"></span></span>
                    </a>

                    <span class="divider-line" aria-hidden="true"></span>

                    <a href="{{ route('contact') }}" class="nav-item">
                        <span class="icon-wrapper" aria-hidden="true">
                            <svg class="ring-svg" viewBox="0 0 74 74">
                                <circle cx="37" cy="37" r="34" fill="none" stroke="#0077ff" stroke-width="2" filter="url(#footerNeonBlueGlow)" />
                                <ellipse cx="58" cy="18" rx="10" ry="6" transform="rotate(25 58 18)" fill="#ffaa00" opacity="0.5" filter="blur(5px)" />
                                <path d="M 37,3 A 34,34 0 0,1 70,30" fill="none" stroke="url(#footerGoldArcGrad)" stroke-width="2.4" stroke-linecap="round" filter="url(#footerNeonGoldGlow)" />
                            </svg>
                            <svg class="inner-icon" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </span>
                        <span class="nav-label-box"><span class="nav-label">Contact Us</span><span class="nav-underline"></span></span>
                    </a>

                    <span class="divider-line" aria-hidden="true"></span>

                    <a href="{{ route('home') }}" class="nav-item">
                        <span class="icon-wrapper" aria-hidden="true">
                            <svg class="ring-svg" viewBox="0 0 74 74">
                                <circle cx="37" cy="37" r="34" fill="none" stroke="#0077ff" stroke-width="2" filter="url(#footerNeonBlueGlow)" />
                                <ellipse cx="58" cy="18" rx="10" ry="6" transform="rotate(25 58 18)" fill="#ffaa00" opacity="0.5" filter="blur(5px)" />
                                <path d="M 37,3 A 34,34 0 0,1 70,30" fill="none" stroke="url(#footerGoldArcGrad)" stroke-width="2.4" stroke-linecap="round" filter="url(#footerNeonGoldGlow)" />
                            </svg>
                            <svg class="inner-icon" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </span>
                        <span class="nav-label-box"><span class="nav-label">Back to Home</span><span class="nav-underline"></span></span>
                    </a>
                </nav>

                <div class="circuit-line-wrapper" aria-hidden="true">
                    <svg class="circuit-svg" viewBox="0 0 1000 30" fill="none" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="footerNeonFluidGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#0044ff" stop-opacity="0" /><stop offset="10%" stop-color="#0066ff" /><stop offset="70%" stop-color="#0099ff" /><stop offset="82%" stop-color="#ffb700" /><stop offset="100%" stop-color="#ffb700" />
                            </linearGradient>
                            <filter id="footerCircuitGlow" x="-20%" y="-50%" width="140%" height="200%">
                                <feGaussianBlur stdDeviation="2" result="blur" /><feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
                            </filter>
                        </defs>
                        <path d="M 20,22 L 725,22 C 755,22 770,8 805,8 L 980,8" stroke="url(#footerNeonFluidGrad)" stroke-width="2.2" filter="url(#footerCircuitGlow)" />
                        <circle cx="215" cy="22" r="2" fill="#ffffff" filter="url(#footerCircuitGlow)" /><circle cx="215" cy="22" r="4" fill="#00d4ff" opacity="0.8" filter="url(#footerCircuitGlow)" />
                        <circle cx="805" cy="8" r="2.2" fill="#ffffff" filter="url(#footerCircuitGlow)" /><circle cx="805" cy="8" r="4.5" fill="#ffb700" opacity="0.9" filter="url(#footerCircuitGlow)" />
                    </svg>
                </div>

                <div class="footer-bar"><span class="copyright-badge"><span class="c-icon">C</span><span>Rivex.uk</span></span></div>
            </div>
        </div>
    </footer>
</body>
</html>
