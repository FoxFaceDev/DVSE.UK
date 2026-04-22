<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DVSE.UK - {{ $title ?? 'Home' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-surface text-on-surface antialiased min-h-screen flex flex-col font-body">

    <!-- Header & Sidebar Component Area -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-200">
        <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if(isset($showBack) && $showBack)
                <a href="{{ url()->previous() }}" class="p-2 -ml-2 text-primary">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                @endif
                <a href="{{ route('home') }}" class="font-heading font-bold text-xl text-primary-dark">DVSE.UK</a>
            </div>
            
            <div x-data="{ open: false }">
                <button @click="open = !open" class="p-2 text-primary focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                
                <!-- Sidebar -->
                <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/50 z-50"></div>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     @click.away="open = false" 
                     class="fixed right-0 top-0 bottom-0 w-64 bg-white shadow-xl z-50 flex flex-col">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                        <span class="font-bold text-primary">Menu</span>
                        <button @click="open = false"><svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <nav class="flex-1 py-4 flex flex-col font-medium text-secondary">
                        <a href="#" class="px-6 py-3 hover:bg-surface-dim hover:text-primary transition-colors">Login</a>
                        <a href="#" class="px-6 py-3 hover:bg-surface-dim hover:text-primary transition-colors">Register</a>
                        <a href="#" class="px-6 py-3 hover:bg-surface-dim hover:text-primary transition-colors">My Test History</a>
                        <a href="#" class="px-6 py-3 hover:bg-surface-dim hover:text-primary transition-colors">My Favorite List</a>
                        <a href="#" class="px-6 py-3 hover:bg-surface-dim hover:text-primary transition-colors">Contact Us</a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 w-full max-w-md mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    <!-- Footer Component Area -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-8">
        <div class="max-w-md mx-auto px-4 text-center">
            <h3 class="font-heading font-bold text-lg text-primary-dark mb-4">DVSE.UK</h3>
            <div class="flex justify-center gap-6 text-sm text-secondary mb-4">
                <a href="#" class="hover:text-primary transition-colors">Contact Us</a>
                <a href="#" class="hover:text-primary transition-colors">About Us</a>
            </div>
            <p class="text-xs text-gray-400 flex items-center justify-center gap-1">
                <span>&copy; DVSE.UK</span>
            </p>
        </div>
    </footer>
</body>
</html>
