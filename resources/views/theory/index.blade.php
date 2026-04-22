<x-layouts.app :showBack="true" title="Theory Test">
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="font-heading font-bold text-2xl text-primary-dark mb-2">Practice Real DVSA-Style Questions and Pass Your Test Faster</h1>
            <p class="text-secondary text-sm">Access official practice materials in your preferred language to boost your confidence.</p>
        </div>

        <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm">
            <label class="block text-sm font-medium text-gray-700 mb-2">Choose Language</label>
            <select class="w-full border-gray-200 rounded-md focus:ring-primary focus:border-primary p-2 bg-surface-dim appearance-none">
                <option value="en">English</option>
                <option value="en-ku">English & Kurdish</option>
            </select>
        </div>

        <div class="space-y-4">
            <a href="{{ route('theory.categories') }}" class="flex items-center gap-4 bg-white hover:bg-surface-dim border border-gray-100 p-5 rounded-lg shadow-sm transition-all group">
                <div class="bg-surface-container rounded-md p-3 text-primary">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="flex-1 font-medium text-gray-800">Theory Test Practice</div>
                <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            
            <a href="#" class="flex items-center gap-4 bg-white hover:bg-surface-dim border border-gray-100 p-5 rounded-lg shadow-sm transition-all group">
                <div class="bg-surface-container rounded-md p-3 text-primary">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div class="flex-1 font-medium text-gray-800">Hazard Perception</div>
                <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>

            <a href="#" class="flex items-center gap-4 bg-white hover:bg-surface-dim border border-gray-100 p-5 rounded-lg shadow-sm transition-all group">
                <div class="bg-surface-container rounded-md p-3 text-primary">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-1 font-medium text-gray-800">Road Signs</div>
                <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>

            <a href="#" class="flex items-center gap-4 bg-primary text-white hover:bg-primary-dark border border-primary-dark p-5 rounded-lg shadow-sm transition-all group">
                <div class="bg-white/20 rounded-md p-3 text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 font-medium text-white">Mock Test Theory</div>
                <svg class="w-5 h-5 text-white group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </a>
        </div>
    </div>
</x-layouts.app>
