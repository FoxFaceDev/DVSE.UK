<x-layouts.app :showBack="false" title="Test Result">
    <div x-data="{
        correct: new URLSearchParams(window.location.search).get('correct') || 0,
        total: new URLSearchParams(window.location.search).get('total') || 0,
        category: new URLSearchParams(window.location.search).get('category') || 'Category',
        get percentage() {
            return this.total > 0 ? Math.round((this.correct / this.total) * 100) : 0;
        },
        get circumference() {
            return 2 * Math.PI * 45;
        },
        get strokeDashoffset() {
            return this.circumference - (this.percentage / 100) * this.circumference;
        }
    }" class="text-center space-y-8 mt-4">

        <div class="inline-block relative w-48 h-48 mx-auto">
            <!-- Circular Chart -->
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="8"></circle>
                <circle cx="50" cy="50" r="45" fill="none" :stroke="percentage >= 80 ? '#16a34a' : (percentage >= 50 ? '#ca8a04' : '#ba1a1a')" stroke-width="8" stroke-linecap="round" :stroke-dasharray="circumference" :stroke-dashoffset="strokeDashoffset" class="transition-all duration-1000 ease-out"></circle>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-4xl font-heading font-bold" x-text="percentage + '%'"></span>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-body text-gray-500 mb-1" x-text="category"></h2>
            <div class="text-sm font-medium text-gray-400 bg-gray-100 px-3 py-1 rounded-full inline-block mb-4">
                <span x-text="correct"></span> / <span x-text="total"></span>
            </div>
            
            <h1 class="text-3xl font-heading font-bold mb-2 text-gray-800" x-text="percentage >= 80 ? 'Well done!' : 'Keep practicing!'"></h1>
            <p class="text-gray-500">You're done with this lesson.</p>
        </div>

        <div class="flex justify-center gap-6 mt-8">
            <div class="flex flex-col items-center p-4 bg-green-50 rounded-lg min-w[100px] border border-green-100">
                <span class="text-xs uppercase tracking-wider text-green-700 font-bold mb-1">Correct</span>
                <span class="text-2xl font-bold text-green-700" x-text="correct"></span>
            </div>
            <div class="flex flex-col items-center p-4 bg-red-50 rounded-lg min-w-[100px] border border-red-100">
                <span class="text-xs uppercase tracking-wider text-red-700 font-bold mb-1">Wrong</span>
                <span class="text-2xl font-bold text-red-700" x-text="total - correct"></span>
            </div>
        </div>

        <div class="pt-8 w-full max-w-xs mx-auto">
            <a href="{{ route('home') }}" class="block w-full py-4 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition shadow shadow-primary/20">Back to Home</a>
        </div>
    </div>
</x-layouts.app>
