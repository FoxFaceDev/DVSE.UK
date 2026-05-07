<x-layouts.app :showBack="false" title="Practice">
    <!-- Receive data from Laravel -->
    <div x-data="testRunner()" x-init="initData({{ Js::from($category) }}, {{ Js::from($questions) }})" class="space-y-6">
        
        <!-- Loading state -->
        <template x-if="!initialized">
            <div class="p-10 text-center"><p class="text-gray-500">Loading questions...</p></div>
        </template>

        <template x-if="initialized && questions.length === 0">
            <div class="p-10 text-center"><p class="text-gray-500">No questions found in this category.</p></div>
        </template>

        <template x-if="initialized && questions.length > 0">
            <div>
                <!-- Top Indicator & Controls -->
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <div class="text-sm font-medium text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                        <span x-text="category.name_en"></span> 
                        <span class="mx-1 text-gray-400">-</span> 
                        <span x-text="String(currentIndex + 1).padStart(2, '0')"></span> / <span x-text="questions.length"></span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="speakCurrentQuestion()" class="p-2 text-primary hover:bg-surface-dim rounded-full transition-colors" title="Listen">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                        </button>
                        <a href="{{ route('frontend.sub_section', $category->subSection) }}" class="p-2 text-error hover:bg-error-container rounded-full transition-colors" title="Exit Practice">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Question Area -->
                <div class="bg-white p-6 rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 mb-6">
                    <!-- Image if exists -->
                    <template x-if="currentQuestion.image_path">
                        <img :src="currentQuestion.image_path" class="w-full h-48 object-cover rounded-lg mb-4 bg-gray-100">
                    </template>
                    
                    <h2 class="font-heading font-medium text-lg text-gray-900 leading-snug" x-text="currentQuestion.text_en"></h2>
                    <template x-if="showKurdish && currentQuestion.text_ku">
                        <h2 class="font-body text-base text-gray-600 mt-2 text-right" dir="rtl" x-text="currentQuestion.text_ku"></h2>
                    </template>
                </div>

                <!-- Choices -->
                <div class="space-y-3 mb-6">
                    <template x-for="(choice, index) in currentQuestion.choices" :key="choice.id">
                        <button 
                            @click="selectChoice(choice)"
                            :disabled="hasAnswered"
                            :class="{
                                'border-gray-200 hover:border-primary hover:bg-surface-dim bg-white': !hasAnswered,
                                'border-success bg-green-50 text-success': hasAnswered && choice.is_correct,
                                'border-error bg-red-50 text-error': hasAnswered && !choice.is_correct && selectedChoiceId === choice.id,
                                'border-gray-200 bg-gray-50 opacity-50': hasAnswered && !choice.is_correct && selectedChoiceId !== choice.id
                            }"
                            class="w-full text-left p-4 rounded-lg border-2 transition-all flex items-start gap-3">
                            
                            <div class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                 :class="{
                                    'border-gray-300': !hasAnswered || (!choice.is_correct && selectedChoiceId !== choice.id),
                                    'border-success bg-success text-white': hasAnswered && choice.is_correct,
                                    'border-error bg-error text-white': hasAnswered && !choice.is_correct && selectedChoiceId === choice.id
                                 }">
                                 <template x-if="hasAnswered && choice.is_correct">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                 </template>
                                 <template x-if="hasAnswered && !choice.is_correct && selectedChoiceId === choice.id">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                 </template>
                            </div>

                            <div class="flex-1">
                                <div class="font-medium" x-text="choice.text_en"></div>
                                <template x-if="showKurdish && choice.text_ku">
                                    <div class="text-sm mt-1 text-right" dir="rtl" x-text="choice.text_ku"></div>
                                </template>
                            </div>
                        </button>
                    </template>
                </div>

                <!-- Explanation (Show if answered) -->
                <div x-show="showExplanation && hasAnswered" x-transition.opacity class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="font-bold text-primary mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Explanation
                    </h4>
                    <p class="text-sm text-gray-800" x-text="currentQuestion.explanation_en || 'No explanation provided.'"></p>
                    <template x-if="showKurdish && currentQuestion.explanation_ku">
                        <p class="text-sm text-gray-800 mt-2 text-right" dir="rtl" x-text="currentQuestion.explanation_ku"></p>
                    </template>
                </div>

                <!-- Controls Bottom -->
                <div class="flex justify-between items-center pt-4">
                    <button @click="prevQuestion()" :disabled="currentIndex === 0" class="px-5 py-3 rounded-md font-medium transition-colors disabled:opacity-50 text-secondary bg-gray-100 hover:bg-gray-200">Wait/Back</button>
                    
                    <template x-if="hasAnswered">
                        <button @click="showExplanation = !showExplanation" class="px-5 py-3 rounded-md font-medium text-primary hover:bg-surface-dim transition-colors">Explain</button>
                    </template>
                    <template x-if="!hasAnswered">
                        <div class="w-[74px]"></div> <!-- Spacer -->
                    </template>

                    <template x-if="currentIndex < questions.length - 1">
                        <button @click="nextQuestion()" :disabled="!hasAnswered" class="px-5 py-3 rounded-md font-medium text-white bg-primary hover:bg-primary-dark transition-colors disabled:opacity-50">Next</button>
                    </template>

                    <template x-if="currentIndex === questions.length - 1">
                        <button @click="finishTest()" :disabled="!hasAnswered" class="px-5 py-3 rounded-md font-medium text-white bg-success hover:bg-green-700 transition-colors disabled:opacity-50">Finish</button>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <script>
        function testRunner() {
            return {
                initialized: false,
                category: null,
                questions: [],
                currentIndex: 0,
                hasAnswered: false,
                selectedChoiceId: null,
                showExplanation: false,
                showKurdish: false, // Could be toggleable or based on selection
                correctCount: 0,
                wrongCount: 0,
                
                initData(category, questions) {
                    this.category = category;
                    this.questions = questions;
                    // Check localstorage or URL param for language
                    this.showKurdish = localStorage.getItem('languagePreference') === 'en-ku';
                    this.initialized = true;
                },
                
                get currentQuestion() {
                    return this.questions[this.currentIndex];
                },
                
                selectChoice(choice) {
                    if (this.hasAnswered) return;
                    this.selectedChoiceId = choice.id;
                    this.hasAnswered = true;
                    if (choice.is_correct) {
                        this.correctCount++;
                    } else {
                        this.wrongCount++;
                    }
                },
                
                nextQuestion() {
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                        this.resetState();
                    }
                },
                
                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                        this.resetState();
                        // Note: going back resets their answer state logically for this simplified flow
                        // For a real test, you'd store all answers in an object map.
                    }
                },
                
                resetState() {
                    this.hasAnswered = false;
                    this.selectedChoiceId = null;
                    this.showExplanation = false;
                },
                
                finishTest() {
                    // Redirect to result page passing score
                    window.location.href = `{{ route('theory.result') }}?correct=${this.correctCount}&total=${this.questions.length}&category=${encodeURIComponent(this.category.name_en)}`;
                },
                
                speakCurrentQuestion() {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                        const utterance = new SpeechSynthesisUtterance(this.currentQuestion.text_en);
                        utterance.lang = 'en-GB';
                        utterance.rate = 0.9;
                        
                        // Add choice text to utterance
                        let fullText = this.currentQuestion.text_en + ". ";
                        if (this.currentQuestion.choices) {
                            this.currentQuestion.choices.forEach((choice, index) => {
                                fullText += `Option ${index + 1}: ${choice.text_en}. `;
                            });
                        }
                        utterance.text = fullText;
                        window.speechSynthesis.speak(utterance);
                    }
                }
            }
        }
    </script>
</x-layouts.app>
