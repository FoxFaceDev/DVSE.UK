<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - DVSE.UK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-on-surface antialiased font-body flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 bg-primary-dark text-white flex flex-col">
        <div class="p-6">
            <h1 class="text-2xl font-heading font-bold">Control Panel</h1>
            <p class="text-primary-100 text-sm mt-1">DVSE.UK Administration</p>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="{{ route('admin.home') }}" class="block px-4 py-3 bg-primary rounded-md shadow-sm font-medium">Dashboard</a>
            <a href="{{ route('admin.questions.index') }}" class="block px-4 py-3 hover:bg-primary rounded-md transition-colors font-medium">Questions</a>
            <a href="{{ route('admin.ads.index') }}" class="block px-4 py-3 hover:bg-primary rounded-md transition-colors font-medium">Advertisements</a>
        </nav>
        <div class="p-4 border-t border-primary/30 text-center text-sm text-gray-300">
            &copy; DVSE.UK
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200">
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
