<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - DVSE.UK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="admin-shell flex min-h-screen font-body text-slate-950 antialiased">

    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 bg-primary-dark text-white flex flex-col">
        <div class="p-6 flex flex-col items-center border-b border-primary/20">
            <img src="{{ asset('images/logo.png') }}" alt="DVSE.UK Logo" style="height: 64px; max-height: 64px; width: auto; object-fit: contain;" class="mb-3 bg-white/10 p-2 rounded-lg">
            <h1 class="text-xl font-heading font-bold text-center">Control Panel</h1>
            <p class="text-primary-100 text-xs mt-1 text-center">DVSE.UK Administration</p>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="{{ route('admin.home') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.home', 'admin.sections.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Dashboard</a>
            <a href="{{ route('admin.questions.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.questions.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Questions</a>
            <a href="{{ route('admin.topics.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.topics.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Topics</a>
            <a href="{{ route('admin.content-pages.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.content-pages.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Learning Pages</a>
            <a href="{{ route('admin.ads.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.ads.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Advertisements</a>
            <a href="{{ route('admin.email-advertisements.create') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.email-advertisements.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Email Campaigns</a>
        </nav>
        <div class="p-4 border-t border-primary/30 text-center text-sm text-gray-300">
            &copy; DVSE.UK
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <header class="border-b border-slate-300 bg-white shadow-sm">
            <div class="px-8 py-4 flex items-center justify-between">
                <h2 class="text-xl font-heading text-primary-dark">{{ $title ?? 'Dashboard' }}</h2>
                <div class="flex items-center gap-4">
                    <div class="text-sm font-medium text-gray-600">
                        {{ auth('admin')->user()->name }}
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 p-8">
            {{ $slot }}
        </main>
    </div>

</body>
</html>
