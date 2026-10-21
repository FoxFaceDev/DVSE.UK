<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Portal - Rivex.uk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body x-data="{ adminMenuOpen: false }" @keydown.escape.window="adminMenuOpen = false" class="admin-shell flex min-h-screen font-body text-slate-950 antialiased">
    <div x-cloak x-show="adminMenuOpen" @click="adminMenuOpen = false" class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"></div>

    <!-- Sidebar -->
    <aside :class="adminMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed inset-y-0 left-0 z-50 flex w-64 flex-shrink-0 flex-col overflow-y-auto bg-gradient-to-b from-blue-700 to-primary-dark text-white transition-transform lg:static lg:translate-x-0">
        <div class="p-6 flex flex-col items-center border-b border-primary/20">
            <img src="{{ asset('images/rivex/logo.png') }}" alt="Rivex.uk Logo" style="height: 64px; max-height: 64px; width: auto; object-fit: contain;" class="mb-3 bg-white/10 p-2 rounded-lg">
            <h1 class="text-xl font-heading font-bold text-center">Control Panel</h1>
            <p class="text-primary-100 text-xs mt-1 text-center">Rivex.uk Administration</p>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="{{ route('admin.home') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.home', 'admin.sections.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Dashboard</a>
            <a href="{{ route('admin.questions.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.questions.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Questions</a>
            <a href="{{ route('admin.topics.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.topics.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Categories</a>
            <a href="{{ route('admin.topics.index', ['revision' => 1]) }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.topics.index') && request()->boolean('revision') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Revision Cards</a>
            <a href="{{ route('admin.content-pages.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.content-pages.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Learning Pages</a>
            <a href="{{ route('admin.mock-tests.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.mock-tests.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Mock Tests</a>
            <a href="{{ route('admin.languages.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.languages.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Languages</a>
            <a href="{{ route('admin.site-settings.edit') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.site-settings.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Website Settings</a>
            <a href="{{ route('admin.hazard-page.edit') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.hazard-page.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Hazard Perception Page</a>
            <a href="{{ route('admin.instructors.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.instructors.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Instructors</a>
            @if(auth('admin')->user()->is_superadmin)<a href="{{ route('admin.users.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">User Accounts</a>@endif
            <a href="{{ route('admin.ads.index') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.ads.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Advertisements</a>
            <a href="{{ route('admin.email-advertisements.create') }}" class="block rounded-md px-4 py-3 font-medium transition-colors {{ request()->routeIs('admin.email-advertisements.*') ? 'bg-primary shadow-sm' : 'hover:bg-primary' }}">Email Campaigns</a>
        </nav>
        <div class="p-4 border-t border-primary/30 text-center text-sm text-gray-300">
            &copy; Rivex.uk
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <header class="border-b border-slate-300 bg-white shadow-sm">
            <div class="flex items-center justify-between px-4 py-4 sm:px-8">
                <button type="button" @click="adminMenuOpen = !adminMenuOpen" :aria-expanded="adminMenuOpen" class="mr-3 rounded-lg bg-blue-50 p-3 text-primary lg:hidden" aria-label="Open administration menu">☰</button>
                <h2 class="text-xl font-heading text-primary-dark">{{ $title ?? 'Dashboard' }}</h2>
                <div class="flex items-center gap-4">
                    <div class="hidden text-sm font-medium text-gray-600 sm:block">
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

        <main class="min-w-0 flex-1 p-4 sm:p-8">
            {{ $slot }}
        </main>
    </div>

</body>
<script>
    document.querySelectorAll('[data-category-sort]').forEach((list) => {
        let draggedRow = null;
        let originalOrder = [];
        const status = list.closest('div')?.querySelector('[data-sort-status]');
        const rows = () => [...list.querySelectorAll('[data-category-id]')];
        const save = async () => {
            if (status) status.textContent = 'Saving order…';
            try {
                const response = await fetch(list.dataset.reorderUrl, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ category_ids: rows().map(row => Number(row.dataset.categoryId)) }),
                });
                if (!response.ok) throw new Error('save');
                if (status) status.textContent = 'Order saved';
                setTimeout(() => { if (status?.textContent === 'Order saved') status.textContent = ''; }, 2200);
            } catch (error) {
                originalOrder.forEach(id => list.append(list.querySelector(`[data-category-id="${id}"]`)));
                if (status) status.textContent = 'Could not save. Order restored.';
            }
        };

        list.querySelectorAll('[data-drag-handle]').forEach((handle) => {
            handle.addEventListener('dragstart', (event) => {
                draggedRow = handle.closest('[data-category-id]');
                originalOrder = rows().map(row => row.dataset.categoryId);
                draggedRow.classList.add('opacity-40', 'bg-blue-50');
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', draggedRow.dataset.categoryId);
            });
            handle.addEventListener('dragend', () => {
                draggedRow?.classList.remove('opacity-40', 'bg-blue-50');
                draggedRow = null;
            });
            handle.addEventListener('keydown', async (event) => {
                if (!event.altKey || !['ArrowUp', 'ArrowDown'].includes(event.key)) return;
                event.preventDefault();
                originalOrder = rows().map(row => row.dataset.categoryId);
                const row = handle.closest('[data-category-id]');
                if (event.key === 'ArrowUp' && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
                if (event.key === 'ArrowDown' && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
                handle.focus();
                await save();
            });
        });
        list.addEventListener('dragover', (event) => {
            if (!draggedRow) return;
            event.preventDefault();
            const target = event.target.closest('[data-category-id]');
            if (!target || target === draggedRow) return;
            const box = target.getBoundingClientRect();
            list.insertBefore(draggedRow, event.clientY < box.top + box.height / 2 ? target : target.nextElementSibling);
        });
        list.addEventListener('drop', async (event) => {
            if (!draggedRow) return;
            event.preventDefault();
            draggedRow.classList.remove('opacity-40', 'bg-blue-50');
            await save();
            draggedRow = null;
        });
    });

    // Keep long editing/upload sessions alive and refresh every form's CSRF token.
    setInterval(async () => {
        try {
            const response = await fetch(@js(route('admin.csrf-token')), { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const { token } = await response.json();
            document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', token);
            document.querySelectorAll('input[name="_token"]').forEach(input => input.value = token);
        } catch (error) {}
    }, 5 * 60 * 1000);
</script>
</html>
