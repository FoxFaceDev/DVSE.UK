<x-layouts.app :showBack="true" :backUrl="route('home')" title="Mock Test Results">
    <div x-data="{ showWrongAnswers: false }" class="max-w-md mx-auto mb-8">
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden relative">
            
            <!-- Dynamic Header Background based on Pass/Fail -->
            <div class="{{ $passed ? 'bg-success' : 'bg-error' }} pt-10 pb-16 px-6 text-center text-white relative transition-colors duration-500">
                @if($passed)
                    <!-- Confetti-like decorative pattern for passing -->
                    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle,white_2px,transparent_2px)] [background-size:24px_24px]"></div>
                @endif
                
                <div class="relative z-10">
                    <div class="w-20 h-20 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-md border border-white/30 mb-4 shadow-sm">
                        @if($passed)
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @else
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @endif
                    </div>
                    <h1 class="text-3xl font-heading font-bold mb-1 tracking-tight">{{ $passed ? 'Congratulations!' : 'Test Failed' }}</h1>
                    <p class="text-white/90 font-medium">
                        {{ $passed ? 'You passed the official mock theory test.' : 'You did not reach the passing mark.' }}
                    </p>
                </div>
            </div>

            <!-- Score Card (Overlapping) -->
            <div class="px-6 relative -mt-8 z-20">
                <div class="bg-white rounded-xl shadow-lg shadow-black/5 border border-gray-100 p-6 flex justify-between items-center">
                    <div class="text-center w-full">
                        <p class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Your Score</p>
                        <div class="text-4xl font-black {{ $passed ? 'text-success' : 'text-error' }}">
                            {{ $correct }} <span class="text-2xl text-gray-400 font-bold">/ {{ $total }}</span>
                        </div>
                    </div>
                    <div class="w-px h-16 bg-gray-200"></div>
                    <div class="text-center w-full">
                        <p class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Pass Mark</p>
                        <div class="text-3xl font-bold text-gray-800">43</div>
                    </div>
                </div>
            </div>

            <!-- Details Section -->
            <div class="p-6 mt-4 space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="text-gray-700 font-medium flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-success"></div>
                        Correct Answers
                    </span>
                    <span class="font-bold text-lg text-success">{{ $correct }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="text-gray-700 font-medium flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-error"></div>
                        Incorrect Answers
                    </span>
                    <span class="font-bold text-lg text-error">{{ $total - $correct }}</span>
                </div>
                
                @if(!$passed)
                    <div class="bg-amber-50 text-amber-800 p-5 rounded-xl border border-amber-200 text-sm shadow-sm mt-6">
                        <p class="font-bold text-base mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Keep Practicing!
                        </p>
                        <p class="text-amber-700 leading-relaxed">Don't be discouraged. Review the standard practice sections to read detailed explanations for each topic before trying the mock test again.</p>
                    </div>
                @endif
            </div>

            @if(count($reviews))
                <div class="px-6 pb-6">
                    <button type="button" @click="showWrongAnswers = !showWrongAnswers" :aria-expanded="showWrongAnswers" class="flex w-full items-center justify-between rounded-xl bg-gradient-to-r from-red-600 to-rose-500 px-5 py-4 font-bold text-white shadow-lg shadow-red-200 transition hover:-translate-y-0.5 hover:shadow-xl">
                        <span class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.6 2.5 17.1A2 2 0 0 0 4.2 20h15.6a2 2 0 0 0 1.7-2.9L13.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
                            </span>
                            <span x-text="showWrongAnswers ? 'Hide wrong answers' : 'See wrong answers ({{ count($reviews) }})'"></span>
                        </span>
                        <svg class="h-5 w-5 transition-transform" :class="showWrongAnswers && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                    </button>

                    <div x-cloak x-show="showWrongAnswers" x-transition class="mt-5 space-y-4 text-left">
                        <h2 class="text-xl font-bold text-gray-900">Wrong answers</h2>
                        @foreach($reviews as $review)
                            <article class="rounded-xl border border-red-100 bg-red-50/40 p-4">
                                @if($review['question_image'])<img src="{{ $review['question_image'] }}" alt="Question" class="mb-3 max-h-44 w-full rounded bg-white object-contain">@endif
                                <h3 class="font-bold text-gray-900">{{ $review['question'] }}</h3>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-lg bg-white p-3 text-red-700"><p class="text-xs font-bold uppercase">Your answer</p>@if($review['selected_image'])<img src="{{ $review['selected_image'] }}" class="mt-2 h-24 w-full object-contain">@endif<p>{{ $review['selected'] ?: 'Not answered' }}</p></div>
                                    <div class="rounded-lg bg-white p-3 text-green-700"><p class="text-xs font-bold uppercase">Correct answer</p>@if($review['correct_image'])<img src="{{ $review['correct_image'] }}" class="mt-2 h-24 w-full object-contain">@endif<p>{{ $review['correct'] }}</p></div>
                                </div>
                                @if($review['explanation'])<p class="mt-3 text-sm text-gray-700">{{ $review['explanation'] }}</p>@endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="p-6 pt-0 flex gap-3">
                <a href="{{ route('home') }}" class="flex-1 py-4 bg-gray-100 hover:bg-gray-200 text-gray-800 text-center rounded-xl font-bold transition-all shadow-sm">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
