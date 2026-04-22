<x-layouts.app :showBack="false">
    <div class="space-y-4">
        <!-- Banner/Promo Area (optional representation of space) -->
        <div class="bg-primary text-white rounded-xl p-6 shadow-md shadow-primary/20 relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="font-heading font-bold text-2xl mb-2">Ready to start?</h2>
                <p class="text-sm opacity-90">Select a mode below to begin.</p>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        </div>

        <!-- Stacked Action Buttons -->
        <a href="{{ route('theory.index') }}" class="flex items-center gap-4 bg-white hover:bg-surface-dim border border-gray-100 p-5 rounded-lg shadow-sm transition-all group">
            <div class="bg-surface-container rounded-md p-3 text-primary group-hover:text-primary-dark transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="flex-1 font-medium text-gray-800 group-hover:text-primary-dark">Theory Test Practice</div>
            <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="#" class="flex items-center gap-4 bg-white hover:bg-surface-dim border border-gray-100 p-5 rounded-lg shadow-sm transition-all group">
            <div class="bg-surface-container rounded-md p-3 text-primary group-hover:text-primary-dark transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="flex-1 font-medium text-gray-800 group-hover:text-primary-dark">Driving Instructors</div>
            <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="#" class="flex items-center gap-4 bg-white hover:bg-surface-dim border border-gray-100 p-5 rounded-lg shadow-sm transition-all group">
            <div class="bg-surface-container rounded-md p-3 text-primary group-hover:text-primary-dark transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 font-medium text-gray-800 group-hover:text-primary-dark">Life in the UK Test</div>
            <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="#" class="flex items-center gap-4 bg-white hover:bg-surface-dim border border-gray-100 p-5 rounded-lg shadow-sm transition-all group">
            <div class="bg-surface-container rounded-md p-3 text-primary group-hover:text-primary-dark transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="flex-1 font-medium text-gray-800 group-hover:text-primary-dark">Find The Best Car Insurance</div>
            <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</x-layouts.app>
