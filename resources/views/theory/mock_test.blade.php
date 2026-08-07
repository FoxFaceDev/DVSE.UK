<x-layouts.app :showBack="false" title="Mock Test Theory">
    <!-- Receive data from Laravel -->
    <div x-data="mockTestRunner()" x-init="initData({{ Js::from($questions) }}, {{ Js::from($languages) }})" class="space-y-6 max-w-md mx-auto">
        
        <!-- Loading state -->
        <template x-if="!initialized">
            <div class="p-10 text-center"><p class="text-gray-500 animate-pulse font-medium">Preparing official exam...</p></div>
        </template>

        <template x-if="initialized && questions.length === 0">
            <div class="p-10 text-center"><p class="text-gray-500">No questions found.</p></div>
        </template>

        <template x-if="initialized && questions.length > 0">
            <div>
                <!-- Top Indicator & Timer -->
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <div class="text-sm font-bold text-gray-800 bg-gray-100 px-3 py-1.5 rounded-lg flex items-center gap-2">
                        <span x-text="String(displayQuestionNumber).padStart(2, '0')"></span> / <span x-text="questions.length"></span>
                    </div>
                    
                    <div class="flex items-center gap-2 px-4 py-1.5 rounded-lg font-bold shadow-sm border transition-colors"
                         :class="timeLeft <= 300 ? 'bg-red-50 text-red-600 border-red-200 animate-pulse' : 'bg-white text-gray-800 border-gray-200'">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span x-text="formattedTime" class="tracking-widest"></span>
                    </div>

                    <div class="flex gap-2">
                        <select x-model="languagePreference" @change="localStorage.setItem('languagePreference', languagePreference)" class="max-w-28 rounded-md border-gray-300 py-1 text-xs"><template x-for="language in languages" :key="language.code"><option :value="language.code" x-text="language.name"></option></template></select>
                        <button @click="confirmExit()" class="p-2 text-gray-400 hover:text-error hover:bg-error-container rounded-full transition-colors" title="Exit Exam">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-6 overflow-hidden">
                    <div class="bg-primary h-1.5 rounded-full transition-all duration-300" :style="'width: ' + ((displayQuestionNumber / questions.length) * 100) + '%'"></div>
                </div>

                <!-- ============ QUESTION CARD ============ -->
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
                                        <iframe :key="'yt-' + realQuestionIndex" class="w-full h-full rounded-lg" :src="'https://www.youtube.com/embed/' + getYoutubeId(currentQuestion.media_source) + '?autoplay=1&mute=1'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                </template>
                                <template x-if="!getYoutubeId(currentQuestion.media_source)">
                                    <video :key="'vid-' + realQuestionIndex" :src="currentQuestion.media_source" autoplay muted controls playsinline preload="auto"
                                        class="w-full max-h-64 rounded-lg bg-gray-100 object-contain"></video>
                                </template>
                            </div>
                        </template>
                        
                        <h2 class="font-heading font-medium text-lg text-gray-900 leading-snug" x-text="currentQuestion.text_en"></h2>
                        <p x-show="languagePreference !== 'en' && translated(currentQuestion, 'text')" class="mt-3 border-t pt-3" :dir="languageDirection" x-text="translated(currentQuestion, 'text')"></p>
                    </div>

                    <!-- Choices -->
                    <div class="mb-6" :class="currentQuestion.question_type === 'image_answers' ? 'grid grid-cols-2 gap-3' : 'space-y-3'">
                        <template x-for="(choice, index) in currentQuestion.choices" :key="choice.id">
                            <button 
                                @click="selectChoice(choice)"
                                :class="{
                                    'border-gray-200 hover:border-primary hover:bg-surface-dim bg-white': selectedChoiceId !== choice.id,
                                    'border-primary bg-primary/5 text-primary-dark ring-1 ring-primary shadow-sm': selectedChoiceId === choice.id
                                }"
                                class="w-full text-left p-4 rounded-lg border-2 transition-all flex items-start gap-3 group">
                                
                                <div class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                     :class="{
                                        'border-gray-300 group-hover:border-primary': selectedChoiceId !== choice.id,
                                        'border-primary bg-primary text-white': selectedChoiceId === choice.id
                                     }">
                                     <template x-if="selectedChoiceId === choice.id">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                     </template>
                                </div>

                                <div class="flex-1">
                                    <template x-if="choice.image_path"><img :src="choice.image_path" :alt="choice.text_en || 'Image answer'" class="mb-2 aspect-square w-full rounded-lg object-contain bg-gray-50"></template>
                                    <div class="font-medium" x-text="choice.text_en"></div>
                                    <div x-show="languagePreference !== 'en' && translated(choice, 'text')" class="mt-1 text-sm" :dir="languageDirection" x-text="translated(choice, 'text')"></div>
                                </div>
                            </button>
                        </template>
                    </div>

                    <!-- Controls Bottom -->
                    <div class="flex justify-between items-center pt-4">
                        <button @click="prevQuestion()" :disabled="realQuestionIndex === 0" class="px-5 py-3 rounded-md font-medium transition-colors disabled:opacity-50 text-secondary bg-gray-100 hover:bg-gray-200">Back</button>

                        <template x-if="realQuestionIndex < questions.length - 1">
                            <button @click="nextQuestion()" :disabled="!hasAnswered" class="px-8 py-3 rounded-md font-bold text-white bg-primary hover:bg-primary-dark transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">Next</button>
                        </template>

                        <template x-if="realQuestionIndex === questions.length - 1">
                            <button @click="finishTest()" :disabled="!hasAnswered" class="px-8 py-3 rounded-md font-bold text-white bg-success hover:bg-green-700 transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed">Submit Exam</button>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        <!-- Exit Modal Overlay -->
        <template x-if="showExitModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showExitModal = false"></div>
                
                <!-- Modal Box -->
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden transform transition-all p-6">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100">
                        <svg class="w-8 h-8 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-center text-gray-900 mb-2">Quit Mock Test?</h3>
                    <p class="text-center text-gray-500 mb-6 text-sm">Are you sure you want to quit? Your timer will stop, and your progress will be permanently lost.</p>
                    
                    <div class="flex gap-3">
                        <button @click="showExitModal = false" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-bold transition-colors">
                            Cancel
                        </button>
                        <button @click="executeExit()" class="flex-1 py-3 bg-error hover:bg-red-700 text-white rounded-xl font-bold transition-colors shadow-sm">
                            Yes, Quit
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function mockTestRunner() {
            return {
                initialized: false,
                showExitModal: false,
                questions: [],
                realQuestionIndex: 0,
                hasAnswered: false,
                selectedChoiceId: null,
                correctCount: 0,
                languages: [],
                languagePreference: 'en',
                
                // Answers mapping
                answers: {}, // map of question_index => choice_id
                
                // Timer state (57 minutes)
                timeLeft: 57 * 60,
                timerInterval: null,
                
                initData(questions, languages) {
                    this.questions = questions;
                    this.languages = languages;
                    this.languagePreference = localStorage.getItem('languagePreference') || 'en';
                    if (!languages.some(language => language.code === this.languagePreference)) this.languagePreference = 'en';
                    
                    // Prepare media_source
                    this.questions.forEach(q => {
                        q.media_source = q.media_path || q.media_url || null;
                    });
                    
                    this.initialized = true;
                    this.startTimer();
                },
                
                get currentQuestion() {
                    return this.questions[this.realQuestionIndex];
                },

                get languageDirection() { return this.languages.find(language => language.code === this.languagePreference)?.direction || 'ltr'; },
                translated(item, field) { return item?.translations?.[this.languagePreference]?.[field] || (this.languagePreference === 'ku' ? item?.[`${field}_ku`] : null) || ''; },
                
                get displayQuestionNumber() {
                    return this.realQuestionIndex + 1;
                },
                
                get formattedTime() {
                    const m = Math.floor(this.timeLeft / 60);
                    const s = this.timeLeft % 60;
                    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                },

                startTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.timeLeft > 0) {
                            this.timeLeft--;
                        } else {
                            clearInterval(this.timerInterval);
                            this.finishTest(); // Auto submit
                        }
                    }, 1000);
                },
                
                selectChoice(choice) {
                    this.selectedChoiceId = choice.id;
                    this.hasAnswered = true;
                    this.answers[this.realQuestionIndex] = {
                        choice_id: choice.id,
                        is_correct: choice.is_correct
                    };
                },
                
                nextQuestion() {
                    if (this.realQuestionIndex < this.questions.length - 1) {
                        this.realQuestionIndex++;
                        this.restoreState();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                
                prevQuestion() {
                    if (this.realQuestionIndex > 0) {
                        this.realQuestionIndex--;
                        this.restoreState();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                
                restoreState() {
                    const ans = this.answers[this.realQuestionIndex];
                    if (ans) {
                        this.hasAnswered = true;
                        this.selectedChoiceId = ans.choice_id;
                    } else {
                        this.hasAnswered = false;
                        this.selectedChoiceId = null;
                    }
                },
                
                confirmExit() {
                    this.showExitModal = true;
                },

                executeExit() {
                    window.location.href = "{{ route('home') }}";
                },

                finishTest() {
                    if(this.timerInterval) clearInterval(this.timerInterval);

                    const answers = {};
                    for (const idx in this.answers) {
                        const question = this.questions[idx];
                        if (question) answers[question.id] = this.answers[idx].choice_id;
                    }

                    fetch('{{ route('theory.mock_test_submit') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            question_ids: this.questions.map(question => question.id),
                            answers: answers
                        })
                    }).then(response => response.json()).then(data => {
                        window.location.href = data.redirect;
                    }).catch(() => {
                        window.location.href = '{{ route('theory.mock_test_result') }}?correct=0&total=' + this.questions.length;
                    });
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
