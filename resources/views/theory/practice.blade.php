<x-layouts.app :showBack="false" title="Practice">
    <div
        x-data="practiceRunner()"
        x-init="initData({{ Js::from($category) }}, {{ Js::from($practiceItems) }}, {{ Js::from($ad) }})"
        class="space-y-6"
    >
        <template x-if="!initialized">
            <div class="p-10 text-center text-gray-500">Loading practice...</div>
        </template>

        <template x-if="initialized && items.length === 0">
            <div class="rounded-xl border border-gray-100 bg-white p-10 text-center text-gray-500">
                No practice content is available in this category yet.
            </div>
        </template>

        <template x-if="initialized && items.length > 0">
            <div>
                <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600">
                        <template x-if="!showingAd">
                            <span>
                                <span x-text="currentItemLabel"></span>
                                <span class="mx-1 text-gray-400">&middot;</span>
                                <span x-text="String(currentIndex + 1).padStart(2, '0')"></span>/<span x-text="items.length"></span>
                            </span>
                        </template>
                        <template x-if="showingAd">
                            <span class="text-amber-600">Sponsored</span>
                        </template>
                    </div>

                    <div class="flex gap-2">
                        <template x-if="!showingAd && speechText">
                            <button @click="speakCurrentItem()" class="rounded-full p-2 text-primary transition-colors hover:bg-surface-dim" title="Listen" aria-label="Listen to this content">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                            </button>
                        </template>
                        <a href="{{ route('frontend.sub_section', $category->subSection) }}" class="rounded-full p-2 text-error transition-colors hover:bg-error-container" title="Exit practice" aria-label="Exit practice">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </div>
                </div>

                <template x-if="showingAd">
                    <div>
                        <div class="relative mb-6 overflow-hidden rounded-xl border border-amber-200 bg-white p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)]">
                            <div class="absolute right-3 top-3 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-700">Ad</div>

                            <template x-if="adData && adData.media_type === 'video'">
                                <div class="mb-4 w-full">
                                    <template x-if="getYoutubeId(adData.media_source)">
                                        <div class="aspect-video">
                                            <iframe class="h-full w-full rounded-lg" :src="youtubeEmbedUrl(adData.media_source, false)" title="Advertisement video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        </div>
                                    </template>
                                    <template x-if="!getYoutubeId(adData.media_source)">
                                        <video :src="adData.media_source" controls playsinline preload="metadata" class="max-h-64 w-full rounded-lg bg-gray-100 object-contain"></video>
                                    </template>
                                </div>
                            </template>

                            <template x-if="adData && adData.media_type !== 'video' && adData.media_source">
                                <img :src="adData.media_source" alt="Advertisement" class="mb-4 max-h-64 w-full rounded-lg bg-gray-100 object-contain">
                            </template>

                            <a :href="adData ? adData.link_url : '#'" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                                <span>Visit advertiser</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>

                        <div class="flex justify-center">
                            <button @click="skipAd()" :disabled="adCountdown > 0" class="rounded-lg px-6 py-3 text-sm font-medium transition-all" :class="adCountdown > 0 ? 'cursor-not-allowed bg-gray-200 text-gray-400' : 'bg-primary text-white shadow-md hover:bg-primary-dark'">
                                <span x-show="adCountdown > 0">Skip in <span x-text="adCountdown"></span>s</span>
                                <span x-show="adCountdown <= 0">Continue &rarr;</span>
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="!showingAd">
                    <div>
                        <template x-if="currentItem.item_type === 'question'">
                            <div>
                                <div class="mb-6 rounded-xl border border-gray-100 bg-white p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)]">
                                    <template x-if="currentItem.media_source && (currentItem.media_type === 'image' || currentItem.media_type === 'gif' || (!currentItem.media_type && currentItem.media_source))">
                                        <img :src="currentItem.media_source" :alt="currentItem.text_en" class="mb-4 max-h-64 w-full rounded-lg bg-gray-100 object-contain">
                                    </template>

                                    <template x-if="currentItem.media_source && currentItem.media_type === 'video'">
                                        <div class="mb-4 w-full">
                                            <template x-if="getYoutubeId(currentItem.media_source)">
                                                <div class="aspect-video">
                                                    <iframe :key="'question-yt-' + currentIndex" class="h-full w-full rounded-lg" :src="youtubeEmbedUrl(currentItem.media_source, true)" title="Question video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                </div>
                                            </template>
                                            <template x-if="!getYoutubeId(currentItem.media_source)">
                                                <video :key="'question-video-' + currentIndex" :src="currentItem.media_source" autoplay muted controls playsinline preload="auto" class="max-h-64 w-full rounded-lg bg-gray-100 object-contain"></video>
                                            </template>
                                        </div>
                                    </template>

                                    <h2 class="font-heading text-lg font-medium leading-snug text-gray-900" x-text="currentItem.text_en"></h2>
                                    <template x-if="showKurdish && currentItem.text_ku">
                                        <h2 class="mt-2 text-right font-body text-base text-gray-600" dir="rtl" x-text="currentItem.text_ku"></h2>
                                    </template>
                                </div>

                                <div class="mb-6 space-y-3">
                                    <template x-for="choice in currentItem.choices" :key="choice.id">
                                        <button
                                            @click="selectChoice(choice)"
                                            :disabled="hasAnswered"
                                            :class="{
                                                'border-gray-200 bg-white hover:border-primary hover:bg-surface-dim': !hasAnswered,
                                                'border-success bg-green-50 text-success': hasAnswered && choice.is_correct,
                                                'border-error bg-red-50 text-error': hasAnswered && !choice.is_correct && selectedChoiceId === choice.id,
                                                'border-gray-200 bg-gray-50 opacity-50': hasAnswered && !choice.is_correct && selectedChoiceId !== choice.id
                                            }"
                                            class="flex w-full items-start gap-3 rounded-lg border-2 p-4 text-left transition-all"
                                        >
                                            <div class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border-2" :class="{
                                                'border-gray-300': !hasAnswered || (!choice.is_correct && selectedChoiceId !== choice.id),
                                                'border-success bg-success text-white': hasAnswered && choice.is_correct,
                                                'border-error bg-error text-white': hasAnswered && !choice.is_correct && selectedChoiceId === choice.id
                                            }">
                                                <template x-if="hasAnswered && choice.is_correct">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </template>
                                                <template x-if="hasAnswered && !choice.is_correct && selectedChoiceId === choice.id">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </template>
                                            </div>

                                            <div class="flex-1">
                                                <div class="font-medium" x-text="choice.text_en"></div>
                                                <template x-if="showKurdish && choice.text_ku">
                                                    <div class="mt-1 text-right text-sm" dir="rtl" x-text="choice.text_ku"></div>
                                                </template>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                <div x-cloak x-show="showQuestionExplanation && hasAnswered" x-transition.opacity class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                                    <h4 class="mb-2 flex items-center gap-2 font-bold text-primary">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Explanation
                                    </h4>
                                    <p class="text-sm text-gray-800" x-text="currentItem.explanation_en || 'No explanation provided.'"></p>
                                    <template x-if="showKurdish && currentItem.explanation_ku">
                                        <p class="mt-2 text-right text-sm text-gray-800" dir="rtl" x-text="currentItem.explanation_ku"></p>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="currentItem.item_type === 'cgi_clips'">
                            <article class="mb-6 overflow-hidden rounded-xl border border-purple-100 bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)]">
                                <div class="flex items-center justify-between border-b border-purple-100 bg-purple-50 px-5 py-3">
                                    <h2 class="font-heading font-bold text-purple-900">CGI clips</h2>
                                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-purple-700" x-text="currentItem.clips.length + (currentItem.clips.length === 1 ? ' clip' : ' clips')"></span>
                                </div>

                                <div class="p-4">
                                    <div class="grid grid-cols-2 gap-2.5">
                                        <template x-for="clip in currentItem.clips" :key="clip.id">
                                            <div class="aspect-video overflow-hidden rounded-lg bg-gray-950 shadow-sm">
                                                <template x-if="getYoutubeId(clip.source)">
                                                    <iframe class="h-full w-full" :src="youtubeEmbedUrl(clip.source, true)" title="CGI clip" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                </template>
                                                <template x-if="!getYoutubeId(clip.source) && isGif(clip.source)">
                                                    <img :src="clip.source" alt="CGI clip" class="h-full w-full object-contain">
                                                </template>
                                                <template x-if="!getYoutubeId(clip.source) && !isGif(clip.source)">
                                                    <video :src="clip.source" autoplay muted loop controls playsinline preload="metadata" class="h-full w-full object-contain"></video>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                    <template x-if="currentItem.text_en || (showKurdish && currentItem.text_ku)">
                                        <div class="mt-5 border-t border-gray-100 pt-5">
                                            <p x-show="currentItem.text_en" class="leading-relaxed text-gray-800" x-text="currentItem.text_en"></p>
                                            <template x-if="showKurdish && currentItem.text_ku">
                                                <p class="mt-3 text-right leading-relaxed text-gray-700" dir="rtl" x-text="currentItem.text_ku"></p>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </article>
                        </template>

                        <template x-if="currentItem.item_type === 'motorway_sign'">
                            <article class="mb-6 overflow-hidden rounded-xl border border-amber-100 bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)]">
                                <div class="border-b border-amber-100 bg-amber-50 px-5 py-3">
                                    <h2 class="font-heading font-bold text-amber-900">Motorway sign</h2>
                                </div>

                                <div class="p-5">
                                    <div class="flex min-h-56 items-center justify-center rounded-xl bg-gray-50 p-5">
                                        <img :src="currentItem.sign_image_path" alt="Motorway sign" class="max-h-72 w-full object-contain">
                                    </div>

                                    <button @click="signExplanationVisible = !signExplanationVisible" :aria-expanded="signExplanationVisible.toString()" class="mt-5 flex min-h-12 w-full items-center justify-center gap-2 rounded-lg bg-primary px-5 py-3 font-bold text-white shadow-sm transition-colors hover:bg-primary-dark">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span x-text="signExplanationVisible ? 'Hide explanation' : 'Show explanation'"></span>
                                    </button>

                                    <div x-cloak x-show="signExplanationVisible" x-transition class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
                                        <p class="leading-relaxed text-gray-800" x-text="currentItem.explanation_en"></p>
                                        <template x-if="showKurdish && currentItem.explanation_ku">
                                            <p class="mt-3 text-right leading-relaxed text-gray-700" dir="rtl" x-text="currentItem.explanation_ku"></p>
                                        </template>
                                    </div>
                                </div>
                            </article>
                        </template>

                        <div class="flex items-center justify-between gap-3 pt-2">
                            <button @click="previousItem()" :disabled="currentIndex === 0" class="min-h-12 rounded-md bg-gray-100 px-5 py-3 font-medium text-secondary transition-colors hover:bg-gray-200 disabled:opacity-50">Back</button>

                            <template x-if="currentItem.item_type === 'question' && hasAnswered">
                                <button @click="showQuestionExplanation = !showQuestionExplanation" class="min-h-12 rounded-md px-3 py-3 font-medium text-primary transition-colors hover:bg-surface-dim">Explain</button>
                            </template>
                            <template x-if="currentItem.item_type !== 'question' || !hasAnswered">
                                <div class="w-16"></div>
                            </template>

                            <template x-if="currentIndex < items.length - 1">
                                <button @click="nextItem()" :disabled="!canContinue" class="min-h-12 rounded-md bg-primary px-5 py-3 font-medium text-white transition-colors hover:bg-primary-dark disabled:opacity-50">Next</button>
                            </template>
                            <template x-if="currentIndex === items.length - 1">
                                <button @click="finishPractice()" :disabled="!canContinue" class="min-h-12 rounded-md bg-success px-5 py-3 font-medium text-white transition-colors hover:bg-green-700 disabled:opacity-50">Finish</button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <script>
        function practiceRunner() {
            return {
                initialized: false,
                category: null,
                items: [],
                currentIndex: 0,
                answers: {},
                hasAnswered: false,
                selectedChoiceId: null,
                showQuestionExplanation: false,
                signExplanationVisible: false,
                showKurdish: false,
                adData: null,
                adPosition: -1,
                showingAd: false,
                adCountdown: 5,
                adTimer: null,
                adShown: false,

                initData(category, items, ad) {
                    this.category = category;
                    this.items = items;
                    this.showKurdish = localStorage.getItem('languagePreference') === 'en-ku';

                    this.items.forEach(item => {
                        if (item.item_type === 'question') {
                            item.media_source = item.media_source || item.media_path || item.media_url || null;
                        }
                    });

                    if (ad && items.length > 2) {
                        this.adData = ad;
                        this.adData.media_source = ad.media_path || ad.media_url || null;
                        this.adPosition = Math.floor(items.length / 2);
                    }

                    this.initialized = true;
                    this.restoreItemState();
                },

                get currentItem() {
                    return this.items[this.currentIndex];
                },

                get currentItemLabel() {
                    if (!this.currentItem) return 'Practice';
                    if (this.currentItem.item_type === 'question') return 'Question';
                    if (this.currentItem.item_type === 'cgi_clips') return 'CGI clips';
                    return 'Motorway sign';
                },

                get canContinue() {
                    return this.currentItem.item_type !== 'question' || this.hasAnswered;
                },

                get totalQuestions() {
                    return this.items.filter(item => item.item_type === 'question').length;
                },

                get correctCount() {
                    return Object.values(this.answers).filter(answer => answer.isCorrect).length;
                },

                get speechText() {
                    if (!this.currentItem) return '';

                    if (this.currentItem.item_type === 'question') {
                        const choices = (this.currentItem.choices || [])
                            .map((choice, index) => `Option ${index + 1}: ${choice.text_en}.`)
                            .join(' ');
                        return `${this.currentItem.text_en}. ${choices}`.trim();
                    }

                    if (this.currentItem.item_type === 'cgi_clips') {
                        return this.currentItem.text_en || '';
                    }

                    return this.signExplanationVisible ? (this.currentItem.explanation_en || '') : '';
                },

                selectChoice(choice) {
                    if (this.hasAnswered) return;

                    this.answers[this.currentItem.id] = {
                        choiceId: choice.id,
                        isCorrect: Boolean(choice.is_correct),
                    };
                    this.selectedChoiceId = choice.id;
                    this.hasAnswered = true;
                },

                nextItem() {
                    if (!this.canContinue || this.currentIndex >= this.items.length - 1) return;

                    this.currentIndex++;
                    this.restoreItemState();

                    if (!this.adShown && this.adData && this.currentIndex === this.adPosition) {
                        this.triggerAd();
                    }
                },

                previousItem() {
                    if (this.showingAd || this.currentIndex === 0) return;
                    this.currentIndex--;
                    this.restoreItemState();
                },

                restoreItemState() {
                    const answer = this.currentItem && this.currentItem.item_type === 'question'
                        ? this.answers[this.currentItem.id]
                        : null;
                    this.hasAnswered = Boolean(answer);
                    this.selectedChoiceId = answer ? answer.choiceId : null;
                    this.showQuestionExplanation = false;
                    this.signExplanationVisible = false;
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

                finishPractice() {
                    if (!this.canContinue) return;
                    window.location.href = `{{ route('theory.result') }}?correct=${this.correctCount}&total=${this.totalQuestions}&category=${encodeURIComponent(this.category.name_en)}`;
                },

                speakCurrentItem() {
                    if (!this.speechText || !('speechSynthesis' in window)) return;
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(this.speechText);
                    utterance.lang = 'en-GB';
                    utterance.rate = 0.9;
                    window.speechSynthesis.speak(utterance);
                },

                getYoutubeId(url) {
                    if (!url) return null;
                    const match = url.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/);
                    return match && match[2].length === 11 ? match[2] : null;
                },

                youtubeEmbedUrl(url, autoplay) {
                    const id = this.getYoutubeId(url);
                    return id ? `https://www.youtube.com/embed/${id}?autoplay=${autoplay ? 1 : 0}&mute=1&playsinline=1` : '';
                },

                isGif(url) {
                    return /\.gif(?:$|[?#])/i.test(url || '');
                },
            };
        }
    </script>
</x-layouts.app>
