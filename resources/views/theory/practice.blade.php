<x-layouts.app :showBack="false" title="Practice">
    <!-- Receive data from Laravel -->
    <div x-data="testRunner()" x-init="initData({{ Js::from($category) }}, {{ Js::from($questions) }}, {{ Js::from($ad) }})" class="space-y-6">
        
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
                        <template x-if="!showingAd">
                            <span>
                                <span x-text="category.name_en"></span> 
                                <span class="mx-1 text-gray-400">-</span> 
                                <span x-text="String(displayQuestionNumber).padStart(2, '0')"></span> / <span x-text="questions.length"></span>
                            </span>
                        </template>
                        <template x-if="showingAd">
                            <span class="text-amber-600">Sponsored</span>
                        </template>
                    </div>
                    <div class="flex gap-2">
                        <template x-if="!showingAd">
                            <button @click="speakCurrentQuestion()" class="p-2 text-primary hover:bg-surface-dim rounded-full transition-colors" title="Listen">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                            </button>
                        </template>
                        <a href="{{ route('frontend.sub_section', $category->subSection) }}" class="p-2 text-error hover:bg-error-container rounded-full transition-colors" title="Exit Practice">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </div>
                </div>

                <!-- ============ AD CARD ============ -->
                <template x-if="showingAd">
                    <div>
                        <div class="bg-white p-6 rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-amber-200 mb-6 relative overflow-hidden">
                            <!-- Sponsored badge -->
                            <div class="absolute top-3 right-3 bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Ad</div>
                            
                            <!-- Ad Media -->
                            <template x-if="adData && adData.media_type === 'video'">
                                <div class="w-full mb-4">
                                    <template x-if="getYoutubeId(adData.media_source)">
                                        <div class="aspect-video">
                                            <iframe class="w-full h-full rounded-lg" :src="'https://www.youtube.com/embed/' + getYoutubeId(adData.media_source)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        </div>
                                    </template>
                                    <template x-if="!getYoutubeId(adData.media_source)">
                                        <video :src="adData.media_source" controls playsinline preload="metadata"
                                            class="w-full max-h-64 object-contain rounded-lg bg-gray-100"></video>
                                    </template>
                                </div>
                            </template>
                            <template x-if="adData && adData.media_type !== 'video' && adData.media_source">
                                <img :src="adData.media_source" alt="Advertisement" 
                                    class="w-full max-h-64 object-contain rounded-lg mb-4 bg-gray-100">
                            </template>

                            <!-- Ad Link -->
                            <a :href="adData ? adData.link_url : '#'" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 text-primary font-medium text-sm hover:underline mt-2">
                                <span>Visit Advertiser</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>

                        <!-- Skip Ad Button with Timer -->
                        <div class="flex justify-center">
                            <button @click="skipAd()" :disabled="adCountdown > 0"
                                class="px-6 py-3 rounded-lg font-medium text-sm transition-all"
                                :class="adCountdown > 0 
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
                                    : 'bg-primary text-white hover:bg-primary-dark shadow-md hover:shadow-lg'">
                                <template x-if="adCountdown > 0">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Skip in <span x-text="adCountdown"></span>s
                                    </span>
                                </template>
                                <template x-if="adCountdown <= 0">
                                    <span>Skip Ad →</span>
                                </template>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- ============ QUESTION CARD ============ -->
                <template x-if="!showingAd">
                    <div>
                        <!-- Question Area -->
                        <div class="bg-white p-6 rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 mb-6">
                            <!-- Media: Image or GIF -->
                            <template x-if="currentQuestion.media_source && (currentQuestion.media_type === 'image' || currentQuestion.media_type === 'gif' || (!currentQuestion.media_type && currentQuestion.media_source))">
                                <img :src="currentQuestion.media_source" 
                                    class="w-full max-h-64 object-contain rounded-lg mb-4 bg-gray-100"
                                    :alt="currentQuestion.text_en">
                            </template>
                            
                            <!-- Media: Video -->
                            <template x-if="currentQuestion.media_source && currentQuestion.media_type === 'video'">
                                <div class="w-full mb-4">
                                    <template x-if="getYoutubeId(currentQuestion.media_source)">
                                        <div class="aspect-video">
                                            <iframe class="w-full h-full rounded-lg" :src="'https://www.youtube.com/embed/' + getYoutubeId(currentQuestion.media_source)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        </div>
                                    </template>
                                    <template x-if="!getYoutubeId(currentQuestion.media_source)">
                                        <video :src="currentQuestion.media_source" controls playsinline preload="metadata"
                                            class="w-full max-h-64 rounded-lg bg-gray-100 object-contain"></video>
                                    </template>
                                </div>
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
                            <button @click="prevQuestion()" :disabled="realQuestionIndex === 0" class="px-5 py-3 rounded-md font-medium transition-colors disabled:opacity-50 text-secondary bg-gray-100 hover:bg-gray-200">Wait/Back</button>
                            
                            <template x-if="hasAnswered">
                                <button @click="showExplanation = !showExplanation" class="px-5 py-3 rounded-md font-medium text-primary hover:bg-surface-dim transition-colors">Explain</button>
                            </template>
                            <template x-if="!hasAnswered">
                                <div class="w-[74px]"></div> <!-- Spacer -->
                            </template>

                            <template x-if="realQuestionIndex < questions.length - 1">
                                <button @click="nextQuestion()" :disabled="!hasAnswered" class="px-5 py-3 rounded-md font-medium text-white bg-primary hover:bg-primary-dark transition-colors disabled:opacity-50">Next</button>
                            </template>

                            <template x-if="realQuestionIndex === questions.length - 1">
                                <button @click="finishTest()" :disabled="!hasAnswered" class="px-5 py-3 rounded-md font-medium text-white bg-success hover:bg-green-700 transition-colors disabled:opacity-50">Finish</button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <script>
        function testRunner() {
            return {
                initialized: false,
                category: null,
                questions: [],
                realQuestionIndex: 0, // Index into the real questions array
                hasAnswered: false,
                selectedChoiceId: null,
                showExplanation: false,
                showKurdish: false,
                correctCount: 0,
                wrongCount: 0,
                
                // Ad state
                adData: null,
                adPosition: -1, // The real question index BEFORE which the ad appears
                showingAd: false,
                adCountdown: 5,
                adTimer: null,
                adShown: false, // Has the ad already been shown?
                
                initData(category, questions, ad) {
                    this.category = category;
                    this.questions = questions;
                    this.showKurdish = localStorage.getItem('languagePreference') === 'en-ku';
                    
                    // Prepare media_source for each question
                    this.questions.forEach(q => {
                        q.media_source = q.media_path || q.media_url || null;
                    });
                    
                    // Setup ad
                    if (ad && questions.length > 2) {
                        this.adData = ad;
                        this.adData.media_source = ad.media_path || ad.media_url || null;
                        // Place ad in the middle: floor(N/2)
                        this.adPosition = Math.floor(questions.length / 2);
                    }
                    
                    this.initialized = true;
                },
                
                get currentQuestion() {
                    return this.questions[this.realQuestionIndex];
                },
                
                get displayQuestionNumber() {
                    return this.realQuestionIndex + 1;
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
                    if (this.realQuestionIndex < this.questions.length - 1) {
                        this.realQuestionIndex++;
                        this.resetState();
                        
                        // Check if we should show the ad
                        if (!this.adShown && this.adData && this.realQuestionIndex === this.adPosition) {
                            this.triggerAd();
                        }
                    }
                },
                
                prevQuestion() {
                    if (this.showingAd) return; // Can't go back during ad
                    if (this.realQuestionIndex > 0) {
                        this.realQuestionIndex--;
                        this.resetState();
                    }
                },
                
                triggerAd() {
                    this.showingAd = true;
                    this.adCountdown = 5;
                    this.adShown = true;
                    
                    this.adTimer = setInterval(() => {
                        this.adCountdown--;
                        if (this.adCountdown <= 0) {
                            clearInterval(this.adTimer);
                            this.adTimer = null;
                        }
                    }, 1000);
                },
                
                skipAd() {
                    if (this.adCountdown > 0) return;
                    this.showingAd = false;
                    if (this.adTimer) {
                        clearInterval(this.adTimer);
                        this.adTimer = null;
                    }
                },
                
                resetState() {
                    this.hasAnswered = false;
                    this.selectedChoiceId = null;
                    this.showExplanation = false;
                },
                
                finishTest() {
                    window.location.href = `{{ route('theory.result') }}?correct=${this.correctCount}&total=${this.questions.length}&category=${encodeURIComponent(this.category.name_en)}`;
                },
                
                speakCurrentQuestion() {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                        const utterance = new SpeechSynthesisUtterance(this.currentQuestion.text_en);
                        utterance.lang = 'en-GB';
                        utterance.rate = 0.9;
                        
                        let fullText = this.currentQuestion.text_en + ". ";
                        if (this.currentQuestion.choices) {
                            this.currentQuestion.choices.forEach((choice, index) => {
                                fullText += `Option ${index + 1}: ${choice.text_en}. `;
                            });
                        }
                        utterance.text = fullText;
                        window.speechSynthesis.speak(utterance);
                    }
                },

                getYoutubeId(url) {
                    if (!url) return null;
                    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                    const match = url.match(regExp);
                    return (match && match[2].length === 11) ? match[2] : null;
                }
            }
        }
    </script>
</x-layouts.app>
