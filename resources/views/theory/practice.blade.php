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

                                    <div>
                                        <p x-show="showKurdish" class="mb-1 text-xs font-bold uppercase tracking-wide text-primary">English</p>
                                        <h2 class="font-heading text-lg font-medium leading-snug text-gray-900" x-text="currentItem.text_en || 'English question not provided.'"></h2>
                                    </div>
                                    <template x-if="showKurdish && currentItem.text_ku">
                                        <div class="mt-4 border-t border-gray-100 pt-3">
                                            <p class="mb-1 text-right text-xs font-bold text-primary" dir="rtl">کوردی</p>
                                            <h2 class="text-right font-body text-base text-gray-700" dir="rtl" x-text="currentItem.text_ku"></h2>
                                        </div>
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
                                    <p class="mb-1 text-xs font-bold uppercase tracking-wide text-primary">English explanation</p>
                                    <p class="text-sm text-gray-800" x-text="currentItem.explanation_en || 'No English explanation provided.'"></p>
                                    <template x-if="showKurdish && currentItem.explanation_ku">
                                        <div class="mt-4 border-t border-blue-200 pt-3">
                                            <p class="mb-1 text-right text-xs font-bold uppercase tracking-wide text-primary" dir="rtl">ڕوونکردنەوەی کوردی</p>
                                            <p class="text-right text-sm text-gray-800" dir="rtl" x-text="currentItem.explanation_ku"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="currentItem.item_type === 'cgi_clips'">
                            <article class="mb-6 overflow-hidden rounded-xl border border-purple-100 bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)]">
                                <div class="flex items-center justify-between border-b border-purple-100 bg-purple-50 px-5 py-3">
                                    <h2 class="font-heading font-bold text-purple-900">Hazard perception</h2>
                                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-purple-700" x-text="cgiStageLabel"></span>
                                </div>

                                <div class="p-4">
                                    <template x-if="cgiStage === 'hazard'">
                                        <div>
                                            <p class="mx-auto mb-3 max-w-3xl text-center text-sm font-medium text-gray-700">Tap the video whenever you see a developing hazard.</p>
                                            <div x-ref="cgiHazardPlayer" class="cgi-hazard-shell mx-auto w-full max-w-3xl overflow-hidden rounded-lg bg-white shadow-sm" :class="{ 'cgi-landscape-fallback': cgiFullscreenFallback }">
                                                <div class="cgi-video-frame relative aspect-video overflow-hidden bg-gray-950">
                                                    <video
                                                        x-ref="cgiHazardVideo"
                                                        :key="'cgi-hazard-' + currentIndex"
                                                        :src="currentItem.clips[0].source"
                                                        playsinline preload="auto"
                                                        @ended="finishCgiHazard()"
                                                        class="h-full w-full object-contain"
                                                    ></video>

                                                    <button x-show="cgiHazardStarted" type="button" @click="placeCgiFlag($event)" class="absolute inset-0 z-10 cursor-crosshair touch-manipulation" aria-label="Flag a developing hazard"></button>

                                                    <div x-show="!cgiHazardStarted" class="absolute inset-0 z-30 flex items-center justify-center bg-black/55 px-5 text-center">
                                                        <button type="button" @click="startCgiHazard()" class="min-h-14 rounded-full bg-white px-7 py-3 font-bold text-purple-900 shadow-lg transition-transform hover:scale-105">
                                                            Start hazard clip with sound
                                                        </button>
                                                    </div>

                                                    <button type="button" @click.stop="toggleCgiFullscreen()" class="absolute bottom-2 right-2 z-40 flex h-10 w-10 items-center justify-center rounded-md bg-black/70 text-white hover:bg-black" title="Landscape fullscreen" aria-label="Open landscape fullscreen">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4m8 0h4v4m0 8v4h-4M8 20H4v-4"/></svg>
                                                    </button>
                                                </div>

                                                <div class="cgi-flag-strip flex min-h-16 items-center gap-1 overflow-x-auto border-t border-gray-200 bg-white px-3 py-2" aria-live="polite">
                                                    <template x-if="cgiFlags.length === 0">
                                                        <span class="text-sm text-gray-400">Your flags will appear here</span>
                                                    </template>
                                                    <template x-for="flag in cgiFlags" :key="flag.id">
                                                        <svg class="h-11 w-11 flex-none text-red-600 drop-shadow-sm" viewBox="0 0 24 24" fill="currentColor" aria-label="Hazard flag">
                                                            <path d="M5 2.5a1 1 0 0 1 2 0V4h11.2a1 1 0 0 1 .9 1.43L17.4 9l1.7 3.57a1 1 0 0 1-.9 1.43H7v7.5a1 1 0 0 1-2 0v-19Z"/>
                                                        </svg>
                                                    </template>
                                                    <span class="ml-auto flex-none rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700"><span x-text="cgiFlags.length"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="cgiStage === 'result'">
                                        <div class="mx-auto flex aspect-video w-full max-w-3xl flex-col items-center justify-center rounded-lg bg-purple-950 px-6 text-center text-white shadow-sm">
                                            <p class="text-sm font-bold uppercase tracking-widest text-purple-200">Hazard score</p>
                                            <p class="mt-3 font-heading text-6xl font-bold"><span x-text="cgiScore"></span><span class="text-3xl text-purple-300">/<span x-text="cgiMaxScore"></span></span></p>
                                            <p class="mt-3 text-sm text-purple-100" x-text="cgiResultMessage"></p>
                                            <p class="mt-1 text-xs text-purple-300"><span x-text="cgiFlags.length"></span> flags placed</p>
                                            <button type="button" @click="startCgiExplanation()" class="mt-6 min-h-12 rounded-lg bg-white px-6 py-3 font-bold text-purple-900 shadow-sm transition-colors hover:bg-purple-50">See explanation video</button>
                                        </div>
                                    </template>

                                    <template x-if="cgiStage === 'explanation'">
                                        <div class="mx-auto aspect-video w-full max-w-3xl overflow-hidden rounded-lg bg-gray-950 shadow-sm">
                                            <video
                                                x-ref="cgiExplanationVideo"
                                                :key="'cgi-explanation-' + currentIndex"
                                                :src="currentItem.clips[1].source"
                                                controls playsinline preload="auto"
                                                class="h-full w-full object-contain"
                                            ></video>
                                        </div>
                                    </template>

                                    <template x-if="cgiStage === 'explanation' && (currentItem.text_en || (showKurdish && currentItem.text_ku))">
                                        <div class="mx-auto mt-5 max-w-3xl border-t border-gray-100 pt-5">
                                            <h3 class="mb-2 font-heading font-bold text-purple-900">Explanation</h3>
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

    <style>
        .cgi-hazard-shell:fullscreen,
        .cgi-hazard-shell:-webkit-full-screen,
        .cgi-hazard-shell.cgi-landscape-fallback {
            display: flex;
            position: fixed;
            inset: 0;
            z-index: 100;
            width: 100vw;
            height: 100vh;
            max-width: none;
            flex-direction: column;
            border-radius: 0;
            background: #000;
        }

        .cgi-hazard-shell:fullscreen .cgi-video-frame,
        .cgi-hazard-shell:-webkit-full-screen .cgi-video-frame,
        .cgi-hazard-shell.cgi-landscape-fallback .cgi-video-frame {
            min-height: 0;
            flex: 1 1 auto;
            aspect-ratio: auto;
        }

        .cgi-hazard-shell:fullscreen .cgi-flag-strip,
        .cgi-hazard-shell:-webkit-full-screen .cgi-flag-strip,
        .cgi-hazard-shell.cgi-landscape-fallback .cgi-flag-strip {
            min-height: 4.75rem;
            flex: 0 0 auto;
        }
    </style>

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
                cgiStage: 'hazard',
                cgiFlags: [],
                cgiFlagSequence: 0,
                cgiScore: 0,
                cgiRangeScores: [],
                cgiHazardStarted: false,
                cgiFullscreenFallback: false,
                languagePreference: 'en',
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
                    this.languagePreference = localStorage.getItem('languagePreference') || 'en';
                    this.showKurdish = this.languagePreference === 'en-ku';

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
                    if (this.currentItem.item_type === 'question') return this.hasAnswered;
                    if (this.currentItem.item_type === 'cgi_clips') return this.cgiStage === 'explanation';
                    return true;
                },

                get cgiStageLabel() {
                    if (this.cgiStage === 'result') return 'Your score';
                    if (this.cgiStage === 'explanation') return 'Explanation video';
                    return 'Hazard video';
                },

                get cgiResultMessage() {
                    const ratio = this.cgiMaxScore > 0 ? this.cgiScore / this.cgiMaxScore : 0;
                    if (ratio === 1) return 'Excellent — you identified every hazard very early.';
                    if (ratio >= 0.6) return 'Good — you identified the developing hazards.';
                    if (this.cgiScore > 0) return 'You identified the hazard, but a little late.';
                    return 'No hazard was identified inside a scoring window.';
                },

                get cgiHazardRanges() {
                    const ranges = Array.isArray(this.currentItem.hazard_windows)
                        ? this.currentItem.hazard_windows.filter(range => Number(range.end) > Number(range.start))
                        : [];

                    if (ranges.length) return ranges;

                    const legacyStart = Number(this.currentItem.hazard_window_start);
                    const legacyEnd = Number(this.currentItem.hazard_window_end);
                    return legacyEnd > legacyStart ? [{ start: legacyStart, end: legacyEnd, points: 5 }] : [];
                },

                get cgiMaxScore() {
                    return this.cgiHazardRanges.reduce(
                        (total, range) => total + Math.max(1, Number.parseInt(range.points, 10) || 5),
                        0
                    ) || 5;
                },

                startCgiHazard() {
                    const video = this.$refs.cgiHazardVideo;
                    if (!video) return;

                    video.muted = false;
                    video.volume = 1;
                    const playback = video.play();
                    if (playback) {
                        playback.then(() => {
                            this.cgiHazardStarted = true;
                        }).catch(() => {
                            this.cgiHazardStarted = false;
                        });
                    } else {
                        this.cgiHazardStarted = true;
                    }
                },

                placeCgiFlag(event) {
                    if (this.cgiStage !== 'hazard') return;

                    const video = this.$refs.cgiHazardVideo;
                    if (!video) return;

                    const time = Number(video.currentTime || 0);
                    const clickScores = this.cgiHazardRanges.map(range => this.cgiScoreForRange(time, range));
                    const score = clickScores.length ? Math.max(...clickScores) : 0;

                    this.cgiRangeScores = clickScores.map((rangeScore, index) =>
                        Math.max(this.cgiRangeScores[index] || 0, rangeScore)
                    );

                    this.cgiFlags.push({
                        id: ++this.cgiFlagSequence,
                        time,
                        score,
                    });
                    this.cgiScore = this.cgiRangeScores.reduce((total, rangeScore) => total + rangeScore, 0);
                },

                cgiScoreForRange(time, range) {
                    const start = Number(range.start);
                    const end = Number(range.end);
                    const maxPoints = Math.max(1, Number.parseInt(range.points, 10) || 5);

                    if (!Number.isFinite(start) || !Number.isFinite(end) || end <= start || time < start || time > end) {
                        return 0;
                    }

                    const zone = Math.min(maxPoints - 1, Math.floor(((time - start) / (end - start)) * maxPoints));
                    return maxPoints - zone;
                },

                finishCgiHazard() {
                    this.exitCgiFullscreen();
                    if (this.cgiStage === 'hazard') this.cgiStage = 'result';
                },

                startCgiExplanation() {
                    this.cgiStage = 'explanation';
                    this.$nextTick(() => {
                        const video = this.$refs.cgiExplanationVideo;
                        if (!video) return;
                        video.muted = false;
                        video.volume = 1;
                        video.play().catch(() => {});
                    });
                },

                async toggleCgiFullscreen() {
                    if (document.fullscreenElement || this.cgiFullscreenFallback) {
                        this.exitCgiFullscreen();
                        return;
                    }

                    const player = this.$refs.cgiHazardPlayer;
                    if (!player) return;

                    if (player.requestFullscreen) {
                        try {
                            await player.requestFullscreen();
                            if (screen.orientation && screen.orientation.lock) {
                                screen.orientation.lock('landscape').catch(() => {});
                            }
                            return;
                        } catch (error) {
                            // Use the full-viewport fallback below.
                        }
                    }

                    this.cgiFullscreenFallback = true;
                    document.body.style.overflow = 'hidden';
                },

                exitCgiFullscreen() {
                    if (document.fullscreenElement && document.exitFullscreen) {
                        document.exitFullscreen().catch(() => {});
                    }
                    if (screen.orientation && screen.orientation.unlock) {
                        try { screen.orientation.unlock(); } catch (error) {}
                    }
                    this.cgiFullscreenFallback = false;
                    document.body.style.removeProperty('overflow');
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
                        return this.cgiStage === 'explanation' ? (this.currentItem.text_en || '') : '';
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
                    this.cgiStage = 'hazard';
                    this.cgiFlags = [];
                    this.cgiFlagSequence = 0;
                    this.cgiScore = 0;
                    this.cgiRangeScores = [];
                    this.cgiHazardStarted = false;
                    this.exitCgiFullscreen();
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
