<x-layouts.app :showBack="false" title="Test Result">
    <div x-data="{
        reviews: [],
        correct: Number(new URLSearchParams(window.location.search).get('correct') || 0),
        total: Number(new URLSearchParams(window.location.search).get('total') || 0),
        category: new URLSearchParams(window.location.search).get('category') || 'Category',
        get percentage() {
            return this.total > 0 ? Math.round((this.correct / this.total) * 100) : 100;
        },
        get circumference() {
            return 2 * Math.PI * 45;
        },
        get strokeDashoffset() {
            return this.circumference - (this.percentage / 100) * this.circumference;
        },
        init() { try { this.reviews = JSON.parse(sessionStorage.getItem('practiceMistakeReview') || '[]'); } catch (error) { this.reviews = []; } }
    }" x-init="init()" class="text-center space-y-8 mt-4">

        <div class="inline-block relative w-48 h-48 mx-auto">
            <!-- Circular Chart -->
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="8"></circle>
                <circle cx="50" cy="50" r="45" fill="none" :stroke="percentage >= 80 ? '#16a34a' : (percentage >= 50 ? '#ca8a04' : '#ba1a1a')" stroke-width="8" stroke-linecap="round" :stroke-dasharray="circumference" :stroke-dashoffset="strokeDashoffset" class="transition-all duration-1000 ease-out"></circle>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <template x-if="total > 0"><span class="text-4xl font-heading font-bold" x-text="percentage + '%'"></span></template>
                <template x-if="total === 0">
                    <svg class="h-16 w-16 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </template>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-body text-gray-500 mb-1" x-text="category"></h2>
            <div x-cloak x-show="total > 0" class="text-sm font-medium text-gray-400 bg-gray-100 px-3 py-1 rounded-full inline-block mb-4">
                <span x-text="correct"></span> / <span x-text="total"></span>
            </div>
            
            <h1 class="text-3xl font-heading font-bold mb-2 text-gray-800" x-text="total === 0 ? 'Lesson complete!' : (percentage >= 80 ? 'Well done!' : 'Keep practicing!')"></h1>
            <p class="text-gray-500">You're done with this lesson.</p>
        </div>

        <div x-cloak x-show="total > 0" class="flex justify-center gap-6 mt-8">
            <div class="flex min-w-[100px] flex-col items-center rounded-lg border border-green-100 bg-green-50 p-4">
                <span class="text-xs uppercase tracking-wider text-green-700 font-bold mb-1">Correct</span>
                <span class="text-2xl font-bold text-green-700" x-text="correct"></span>
            </div>
            <div class="flex flex-col items-center p-4 bg-red-50 rounded-lg min-w-[100px] border border-red-100">
                <span class="text-xs uppercase tracking-wider text-red-700 font-bold mb-1">Wrong</span>
                <span class="text-2xl font-bold text-red-700" x-text="total - correct"></span>
            </div>
        </div>

        <div x-cloak x-show="reviews.length" class="mx-auto max-w-2xl space-y-4 text-left">
            <h2 class="text-xl font-bold text-gray-900">Review your mistakes</h2>
            <template x-for="review in reviews" :key="review.question">
                <article class="rounded-xl border border-red-100 bg-white p-5 shadow-sm">
                    <template x-if="review.media"><img :src="review.media" alt="Question" class="mb-3 max-h-48 w-full rounded bg-gray-50 object-contain"></template>
                    <h3 class="font-bold" x-text="review.question"></h3>
                    <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded bg-red-50 p-3 text-red-700"><strong>Your answer</strong><template x-if="review.selected?.image_path"><img :src="review.selected.image_path" class="my-2 h-24 w-full object-contain"></template><p x-text="review.selected?.text_en || 'Not answered'"></p></div>
                        <div class="rounded bg-green-50 p-3 text-green-700"><strong>Correct answer</strong><template x-if="review.correct?.image_path"><img :src="review.correct.image_path" class="my-2 h-24 w-full object-contain"></template><p x-text="review.correct?.text_en || ''"></p></div>
                    </div>
                    <p x-show="review.explanation" class="mt-3 text-sm text-gray-600" x-text="review.explanation"></p>
                </article>
            </template>
        </div>

        <div class="pt-8 w-full max-w-xs mx-auto">
            <a href="{{ route('home') }}" class="block w-full py-4 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition shadow shadow-primary/20">Back to Home</a>
        </div>
    </div>
</x-layouts.app>
