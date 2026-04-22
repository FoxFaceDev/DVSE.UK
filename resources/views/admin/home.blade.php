<x-layouts.admin title="Dashboard">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('admin.categories.index') }}" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between hover:-translate-y-1 hover:shadow-md transition-all group">
            <div>
                <h3 class="font-bold text-gray-800 group-hover:text-primary">Theory Test Practice</h3>
                <span class="text-sm text-gray-500">Manage categories & questions</span>
            </div>
            <div class="p-3 bg-blue-50 text-primary rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </a>
        <a href="#" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between hover:-translate-y-1 hover:shadow-md transition-all group">
            <div>
                <h3 class="font-bold text-gray-800 group-hover:text-primary">Driving Instructors</h3>
                <span class="text-sm text-gray-500">Manage instructors</span>
            </div>
            <div class="p-3 bg-blue-50 text-primary rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </a>
        <a href="#" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between hover:-translate-y-1 hover:shadow-md transition-all group">
            <div>
                <h3 class="font-bold text-gray-800 group-hover:text-primary">Life in the UK Test</h3>
                <span class="text-sm text-gray-500">Manage materials</span>
            </div>
            <div class="p-3 bg-blue-50 text-primary rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </a>
        <a href="#" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between hover:-translate-y-1 hover:shadow-md transition-all group">
            <div>
                <h3 class="font-bold text-gray-800 group-hover:text-primary">Car Insurance</h3>
                <span class="text-sm text-gray-500">Manage offers</span>
            </div>
            <div class="p-3 bg-blue-50 text-primary rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </a>
    </div>
</x-layouts.admin>
