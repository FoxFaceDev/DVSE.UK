@php
    $backUrl = $backUrlOverride ?? route('home');
    if (! isset($backUrlOverride) && $topic->topicable_type === 'App\Models\Category') {
        $backUrl = route('frontend.category', $topic->topicable_id);
    } elseif (! isset($backUrlOverride) && $topic->topicable_type === 'App\Models\SubSection') {
        $backUrl = route('frontend.sub_section', $topic->topicable_id);
    } elseif (! isset($backUrlOverride) && $topic->topicable_type === 'App\Models\Section') {
        $backUrl = route('frontend.section', $topic->topicable_id);
    }
@endphp
<x-layouts.app :showBack="false" :title="isset($hazardStudyPage) ? 'Hazard learning' : 'Practice'">
    <div
        x-data="practiceRunner()"
        x-init="initData({{ Js::from($topic) }}, {{ Js::from($practiceItems) }}, {{ Js::from($ads) }}, {{ Js::from($languages) }}, {{ Js::from($preferredLanguage?->code ?? 'en') }}, {{ Js::from(isset($hazardStudyPage) ? ['pageId' => $hazardStudyPage->id, 'returnUrl' => $backUrl] : null) }})"
        @fullscreenchange.window="handleCgiFullscreenChange()"
        @webkitfullscreenchange.window="handleCgiFullscreenChange()"
        @keydown.escape.window="closeAdditionalSignModal()"
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

                    <div class="flex items-center gap-2">
                        <template x-if="!showingAd && speechText">
                            <button @click="toggleSpeech()" class="rounded-full p-2 transition-colors hover:bg-surface-dim" :class="isSpeaking ? 'bg-red-50 text-red-600' : 'text-primary'" :title="isSpeaking ? 'Stop reading' : 'Listen'" :aria-label="isSpeaking ? 'Stop reading this content' : 'Listen to this content'">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                            </button>
                        </template>
                        <a href="{{ $backUrl }}" class="rounded-full p-2 text-error transition-colors hover:bg-error-container" title="Exit practice" aria-label="Exit practice">
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
                                            <iframe class="h-full w-full rounded-lg" :src="youtubeEmbedUrl(adData.media_source, true, false)" title="Advertisement video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        </div>
                                    </template>
                                    <template x-if="!getYoutubeId(adData.media_source)">
                                        <video x-ref="adVideo" :src="adData.media_source" autoplay controls playsinline preload="auto" class="max-h-64 w-full rounded-lg bg-gray-100 object-contain"></video>
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
                                        <p x-show="showTranslation" class="mb-1 text-xs font-bold uppercase tracking-wide text-primary">English</p>
                                        <h2 class="font-heading text-lg font-medium leading-snug text-gray-900" x-text="currentItem.text_en || 'English question not provided.'"></h2>
                                    </div>
                                    <template x-if="showTranslation && translated(currentItem, 'text')">
                                        <div class="mt-4 border-t border-gray-100 pt-3">
                                            <p class="mb-1 text-xs font-bold text-primary" :dir="languageDirection" x-text="languageName"></p>
                                            <h2 class="font-body text-base text-gray-700" :dir="languageDirection" x-text="translated(currentItem, 'text')"></h2>
                                        </div>
                                    </template>
                                </div>

                                <div class="mb-6" :class="currentItem.question_type === 'image_answers' ? 'grid grid-cols-2 gap-3' : 'space-y-3'">
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
                                                <template x-if="choice.image_path"><img :src="choice.image_path" :alt="choice.text_en || 'Image answer'" class="mb-2 aspect-square w-full rounded-lg bg-white object-contain"></template>
                                                <div class="font-medium" x-text="choice.text_en"></div>
                                                <template x-if="showTranslation && translated(choice, 'text')">
                                                    <div class="mt-1 text-sm" :dir="languageDirection" x-text="translated(choice, 'text')"></div>
                                                </template>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                <div x-cloak x-show="showQuestionExplanation" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 p-4" @click.self="showQuestionExplanation = false">
                                  <div class="relative max-h-[85vh] w-full max-w-md overflow-y-auto rounded-2xl border border-blue-200 bg-white p-6 shadow-2xl">
                                    <button type="button" @click="showQuestionExplanation = false" class="absolute right-3 top-3 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-2xl text-gray-700 hover:bg-red-50 hover:text-red-600" aria-label="Close explanation">&times;</button>
                                    <h4 class="mb-2 flex items-center gap-2 pr-10 font-bold text-primary">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span x-text="showTranslation ? languageName : 'Explanation'"></span>
                                    </h4>
                                    <div x-show="!showTranslation || !translated(currentItem, 'explanation')">
                                        <p class="mb-1 text-xs font-bold uppercase tracking-wide text-primary">English explanation</p>
                                        <p class="text-sm text-gray-800" x-text="currentItem.explanation_en || 'No English explanation provided.'"></p>
                                    </div>
                                    <template x-if="showTranslation && translated(currentItem, 'explanation')">
                                        <div>
                                            <p class="text-sm text-gray-800" :dir="languageDirection" x-text="translated(currentItem, 'explanation')"></p>
                                        </div>
                                    </template>
                                  </div>
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
                                                        playsinline preload="auto" muted
                                                        @loadstart="startCgiLoading('hazard')"
                                                        @loadedmetadata="updateCgiLoadProgress($event.target, 'hazard')"
                                                        @progress="updateCgiLoadProgress($event.target, 'hazard')"
                                                        @canplay="finishCgiLoading($event.target, 'hazard')"
                                                        @waiting="cgiHazardLoading = true"
                                                        @play="enterCgiFullscreen('hazard')"
                                                        @playing="finishCgiLoading($event.target, 'hazard')"
                                                        x-on:error="cgiHazardLoading = false"
                                                        @ended="finishCgiHazard()"
                                                        class="h-full w-full object-contain"
                                                    ></video>

                                                    <button x-show="cgiHazardStarted" type="button" @click="placeCgiFlag($event)" class="absolute inset-0 z-10 cursor-crosshair touch-manipulation" aria-label="Flag a developing hazard"></button>

                                                    <div x-show="!cgiHazardStarted" class="absolute inset-0 z-30 flex items-center justify-center bg-black/55 px-5 text-center">
                                                        <button x-show="!cgiHazardPlayRequested" type="button" @click="startCgiHazard()" class="min-h-14 rounded-full bg-white px-7 py-3 font-bold text-purple-900 shadow-lg transition-transform hover:scale-105">
                                                            Start hazard clip
                                                        </button>

                                                        <div x-show="cgiHazardPlayRequested" x-transition.opacity class="w-full max-w-56 rounded-2xl bg-black/75 px-5 py-4 text-white shadow-xl backdrop-blur-sm">
                                                            <div class="mx-auto h-11 w-11 animate-spin rounded-full border-4 border-white/25 border-t-white"></div>
                                                            <p class="mt-3 text-sm font-bold">Preparing hazard clip</p>
                                                            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/20">
                                                                <div class="h-full rounded-full bg-white transition-[width] duration-300" :style="'width:' + cgiHazardLoadProgress + '%'"></div>
                                                            </div>
                                                            <p class="mt-1.5 text-xs text-white/70"><span x-text="Math.round(cgiHazardLoadProgress)"></span>% buffered</p>
                                                        </div>
                                                    </div>

                                                    <div x-show="cgiHazardStarted && cgiHazardLoading" x-transition.opacity class="pointer-events-none absolute inset-0 z-30 flex items-center justify-center bg-black/45 px-5 text-center">
                                                        <div class="w-full max-w-52 rounded-2xl bg-black/75 px-5 py-4 text-white shadow-xl backdrop-blur-sm">
                                                            <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-white/25 border-t-white"></div>
                                                            <p class="mt-3 text-sm font-bold">Buffering video</p>
                                                            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/20">
                                                                <div class="h-full rounded-full bg-white transition-[width] duration-300" :style="'width:' + cgiHazardLoadProgress + '%'"></div>
                                                            </div>
                                                        </div>
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
                                                        <svg class="h-7 w-7 flex-none text-red-600 drop-shadow-sm" viewBox="0 0 24 24" fill="currentColor" aria-label="Hazard flag">
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
                                            <p x-show="cgiInvalidResponse && showKurdish" x-cloak class="mt-2 text-sm text-purple-100" dir="rtl">بەردەوام یان بە شێوەیەکی دووبارە کلیکت کرد، بۆیە کلیپەکە وەستێنرا و نمرەکەی سفرە.</p>
                                            <p class="mt-1 text-xs text-purple-300"><span x-text="cgiFlags.length"></span> flags placed</p>
                                            <button type="button" @click="startCgiExplanation()" class="mt-6 min-h-12 rounded-lg bg-white px-6 py-3 font-bold text-purple-900 shadow-sm transition-colors hover:bg-purple-50">See explanation video</button>
                                        </div>
                                    </template>

                                    <template x-if="cgiStage === 'explanation'">
                                        <div
                                            x-ref="cgiExplanationPlayer"
                                            class="cgi-explanation-shell mx-auto w-full max-w-3xl overflow-hidden rounded-lg bg-white shadow-sm"
                                            :class="{ 'cgi-landscape-fallback': cgiFullscreenFallback && cgiFullscreenTarget === 'explanation' }"
                                        >
                                            <div class="cgi-explanation-video-frame relative aspect-video overflow-hidden bg-gray-950">
                                                <video
                                                    x-ref="cgiExplanationVideo"
                                                    :key="'cgi-explanation-' + currentIndex"
                                                    :src="currentItem.clips[1].source"
                                                    disablepictureinpicture
                                                    playsinline webkit-playsinline preload="auto" tabindex="0"
                                                    role="button"
                                                    aria-label="Tap to pause or play the explanation video"
                                                    class="h-full w-full cursor-pointer touch-manipulation object-contain"
                                                    @click="toggleCgiExplanationPlayback()"
                                                    @keydown.space.prevent="toggleCgiExplanationPlayback()"
                                                    @keydown.enter.prevent="toggleCgiExplanationPlayback()"
                                                    @loadstart="startCgiLoading('explanation')"
                                                    @loadedmetadata="syncCgiExplanationVideo($event.target)"
                                                    @durationchange="syncCgiExplanationVideo($event.target)"
                                                    @progress="updateCgiLoadProgress($event.target, 'explanation')"
                                                    @canplay="finishCgiLoading($event.target, 'explanation')"
                                                    @timeupdate="syncCgiExplanationVideo($event.target)"
                                                    @waiting="cgiExplanationLoading = true"
                                                    @seeking="cgiExplanationLoading = true; cgiExplanationTime = $event.target.currentTime"
                                                    @seeked="finishCgiSeek($event.target)"
                                                    @play="cgiExplanationPaused = false; enterCgiFullscreen('explanation')"
                                                    @playing="cgiExplanationPaused = false; finishCgiLoading($event.target, 'explanation')"
                                                    @pause="cgiExplanationPaused = true"
                                                    x-on:error="cgiExplanationLoading = false"
                                                    @ended="exitCgiFullscreen()"
                                                ></video>

                                                <div x-show="cgiExplanationLoading" x-transition.opacity class="pointer-events-none absolute inset-0 z-[105] flex items-center justify-center bg-black/45 px-5 text-center">
                                                    <div class="w-full max-w-56 rounded-2xl bg-black/75 px-5 py-4 text-white shadow-xl backdrop-blur-sm">
                                                        <div class="mx-auto h-11 w-11 animate-spin rounded-full border-4 border-white/25 border-t-white"></div>
                                                        <p class="mt-3 text-sm font-bold">Loading explanation</p>
                                                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/20">
                                                            <div class="h-full rounded-full bg-white transition-[width] duration-300" :style="'width:' + cgiExplanationLoadProgress + '%'"></div>
                                                        </div>
                                                        <p class="mt-1.5 text-xs text-white/70"><span x-text="Math.round(cgiExplanationLoadProgress)"></span>% buffered</p>
                                                    </div>
                                                </div>

                                                <div x-show="cgiExplanationPaused && !cgiExplanationLoading && !cgiScrubbing" x-transition.opacity class="pointer-events-none absolute inset-0 z-[90] flex items-center justify-center bg-black/15">
                                                    <span class="flex h-18 w-18 items-center justify-center rounded-full bg-black/70 text-white shadow-xl backdrop-blur-sm">
                                                        <svg class="ml-1 h-8 w-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                                                    </span>
                                                </div>

                                                <button type="button" @click.stop="toggleCgiFullscreen('explanation')" class="absolute right-2 top-2 z-[110] flex h-11 w-11 touch-manipulation items-center justify-center rounded-md bg-black/75 text-white shadow-md hover:bg-black" title="Fullscreen with timeline" aria-label="Open video and timeline fullscreen">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4m8 0h4v4m0 8v4h-4M8 20H4v-4"/></svg>
                                                </button>
                                            </div>

                                            <!-- Timeline bar -->
                                            <div class="cgi-timeline-panel w-full select-none bg-white p-3" x-show="cgiVideoDuration > 0" x-cloak>
                                                <div class="relative w-full cursor-pointer overflow-visible"
                                                     style="height: 48px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px"
                                                     x-ref="cgiTimeline">
                                                    <input
                                                        x-model.number="cgiExplanationTime"
                                                        type="range"
                                                        min="0"
                                                        :max="cgiVideoDuration"
                                                        step="0.01"
                                                        class="absolute inset-0 z-40 h-full w-full cursor-pointer opacity-0"
                                                        style="touch-action: none"
                                                        aria-label="Explanation video timeline"
                                                        :aria-valuemin="0"
                                                        :aria-valuemax="cgiVideoDuration"
                                                        :aria-valuenow="cgiExplanationTime"
                                                        @pointerdown="cgiStartScrub()"
                                                        @pointerup="cgiStopScrub()"
                                                        @pointercancel="cgiStopScrub()"
                                                        @input="seekCgiTimeline($event.target.value)"
                                                        @change="cgiStopScrub()"
                                                    >

                                                    <!-- Playhead line -->
                                                    <div class="absolute top-0 bottom-0 z-30 pointer-events-none"
                                                         :style="'left:' + (cgiVideoDuration > 0 ? (cgiExplanationTime / cgiVideoDuration * 100) : 0) + '%; width: 2px; background: #000'">
                                                        <!-- Playhead dot -->
                                                        <div style="position:absolute; top:-6px; left:-7px; width:16px; height:16px; border-radius:50%; background:#000; box-shadow: 0 1px 4px rgba(0,0,0,0.4)"></div>
                                                    </div>

                                                    <!-- Scoring blocks -->
                                                    <template x-for="(range, ri) in cgiHazardRanges" :key="'tl-' + ri">
                                                        <div class="absolute top-0 bottom-0 z-10 flex overflow-hidden"
                                                             :style="'left:' + (range.start / cgiVideoDuration * 100) + '%; width:' + ((range.end - range.start) / cgiVideoDuration * 100) + '%'">
                                                            <template x-for="si in range.points" :key="si">
                                                                <div class="flex-1 flex items-center justify-center font-bold text-white"
                                                                     :style="'font-size:11px; border-right:1px solid rgba(255,255,255,0.3); background:' + ['#b91c1c','#dc2626','#ef4444','#f87171','#fca5a5','#fecaca'][Math.min(si - 1, 5)]">
                                                                    <span x-text="range.points - si + 1"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Replay only the flags the learner placed during the hazard video. -->
                                                    <template x-for="flag in cgiFlags" :key="'review-flag-' + flag.id">
                                                        <template x-if="cgiExplanationTime >= flag.time">
                                                            <div class="absolute z-20 pointer-events-none"
                                                                 :style="'left:' + (flag.time / cgiVideoDuration * 100) + '%; top:-8px; transform:translateX(-50%)'"
                                                                 :aria-label="'Your hazard flag at ' + flag.time.toFixed(1) + ' seconds'">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#dc2626">
                                                                    <path d="M5 2.5a1 1 0 0 1 2 0V4h11.2a1 1 0 0 1 .9 1.43L17.4 9l1.7 3.57a1 1 0 0 1-.9 1.43H7v7.5a1 1 0 0 1-2 0v-19Z"/>
                                                                </svg>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="cgiStage === 'explanation' && (currentItem.text_en || (showTranslation && translated(currentItem, 'text')))">
                                        <div class="mx-auto mt-5 max-w-3xl border-t border-gray-100 pt-5">
                                            <div class="mb-2 flex items-center justify-between gap-3">
                                                <!-- Kurdish UI label retained for translation reference: ڕوونکردنەوە -->
                                                <h3 class="font-heading font-bold text-purple-900" x-text="showTranslation ? languageName : 'Explanation'"></h3>
                                            </div>
                                            <p x-show="!showTranslation && currentItem.text_en" class="leading-relaxed text-gray-800" x-text="currentItem.text_en"></p>
                                            <template x-if="showTranslation && translated(currentItem, 'text')">
                                                <p class="mt-3 leading-relaxed text-gray-700" :dir="languageDirection" x-text="translated(currentItem, 'text')"></p>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </article>
                        </template>

                        <template x-if="currentItem.item_type === 'motorway_sign'">
                            <article class="mb-6 overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-[0_8px_28px_-12px_rgba(0,74,153,0.22)]">
                                <div class="flex items-center gap-3 border-b border-blue-100 bg-primary/5 px-5 py-4">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-white">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"/></svg>
                                    </span>
                                    <h2 class="font-heading font-bold text-primary-dark">Motorway sign guide</h2>
                                </div>

                                <div class="p-5 sm:p-6">
                                    <div class="flex min-h-56 items-center justify-center rounded-2xl border border-slate-100 bg-slate-50 p-6">
                                        <img :src="currentItem.sign_image_path" alt="Motorway sign" class="max-h-80 w-full object-contain drop-shadow-sm">
                                    </div>

                                    <section class="mt-6 border-l-4 border-primary pl-4">
                                        <h3 class="font-heading text-lg font-bold text-gray-950">About this sign</h3>
                                        <p x-show="!showTranslation" class="mt-2 whitespace-pre-line leading-7 text-gray-700" x-text="currentItem.explanation_en"></p>
                                        <template x-if="showTranslation && translated(currentItem, 'explanation')">
                                            <p class="mt-2 whitespace-pre-line leading-7 text-gray-700" :dir="languageDirection" x-text="translated(currentItem, 'explanation')"></p>
                                        </template>
                                    </section>

                                    <section class="mt-6 rounded-xl bg-primary/5 p-4">
                                        <h3 class="font-heading text-lg font-bold text-primary-dark">What to do</h3>
                                        <p x-show="!showTranslation" class="mt-2 whitespace-pre-line leading-7 text-gray-700" x-text="currentItem.what_to_do_en"></p>
                                        <template x-if="showTranslation && translated(currentItem, 'what_to_do')">
                                            <p class="mt-2 whitespace-pre-line leading-7 text-gray-700" :dir="languageDirection" x-text="translated(currentItem, 'what_to_do')"></p>
                                        </template>
                                    </section>

                                    <template x-if="Array.isArray(currentItem.additional_sign_images) && currentItem.additional_sign_images.length">
                                        <section class="mt-7 border-t border-gray-100 pt-6">
                                            <div :dir="languageDirection">
                                                <!-- Kurdish default retained for existing content: نیشانە زیادەکان کە لەوانەیە ببینیت -->
                                                <h3 class="font-heading text-lg font-bold leading-snug text-gray-950" x-text="showTranslation ? translated(currentItem, 'additional_signs_title') : (translated(currentItem, 'additional_signs_title') || 'Additional signs you can expect')"></h3>
                                                <p class="mt-1 text-sm text-secondary" x-text="showTranslation ? translated(currentItem, 'additional_signs_description') : (translated(currentItem, 'additional_signs_description') || 'These signs may accompany the main road sign.')"></p>
                                            </div>
                                            <div class="mt-4 grid grid-cols-3 gap-3">
                                                <template x-for="(image, index) in currentItem.additional_sign_images" :key="image">
                                                    <button
                                                        type="button"
                                                        @click="openAdditionalSignModal(image, index)"
                                                        class="flex aspect-square cursor-zoom-in items-center justify-center rounded-xl border border-gray-200 bg-white p-2.5 shadow-sm transition hover:border-primary hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                                        :aria-label="`Open additional road sign ${index + 1}`"
                                                    >
                                                        <img :src="image" :alt="`Additional road sign ${index + 1}`" class="h-full w-full object-contain">
                                                    </button>
                                                </template>
                                            </div>
                                        </section>
                                    </template>
                                </div>
                            </article>
                        </template>

                        <template x-if="selectedAdditionalSign">
                            <div
                                class="fixed inset-0 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
                                style="z-index: 9999"
                                role="dialog"
                                aria-modal="true"
                                aria-label="Additional road sign image"
                                @click.self="closeAdditionalSignModal()"
                            >
                                <div class="relative flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                                    <button type="button" @click="closeAdditionalSignModal()" class="absolute right-3 top-3 z-10 flex h-11 w-11 items-center justify-center rounded-full bg-gray-950/85 text-2xl text-white shadow-lg ring-2 ring-white transition hover:scale-105 hover:bg-red-600" aria-label="Close image">&times;</button>
                                    <div class="flex min-h-0 flex-1 items-center justify-center bg-slate-50 p-4 sm:p-6">
                                        <img
                                            :src="selectedAdditionalSign.src"
                                            :alt="`Additional road sign ${selectedAdditionalSign.index}`"
                                            class="max-h-[72vh] max-w-full object-contain"
                                        >
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="flex items-center justify-between gap-3 pt-2">
                            <button @click="previousItem()" :disabled="currentIndex === 0" class="min-h-12 rounded-md bg-gray-100 px-5 py-3 font-medium text-secondary transition-colors hover:bg-gray-200 disabled:opacity-50">Back</button>

                            <template x-if="currentItem.item_type === 'question'">
                                <button @click="showQuestionExplanation = !showQuestionExplanation" class="min-h-12 rounded-md px-3 py-3 font-medium text-primary transition-colors hover:bg-surface-dim">Explain</button>
                            </template>
                            <template x-if="currentItem.item_type !== 'question'">
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
        .cgi-hazard-shell.cgi-landscape-fallback,
        .cgi-explanation-shell:fullscreen,
        .cgi-explanation-shell:-webkit-full-screen,
        .cgi-explanation-shell.cgi-landscape-fallback {
            display: flex;
            position: fixed;
            inset: 0;
            z-index: 100;
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            max-width: none;
            flex-direction: column;
            border-radius: 0;
            background: #000;
        }

        .cgi-hazard-shell:fullscreen .cgi-video-frame,
        .cgi-hazard-shell:-webkit-full-screen .cgi-video-frame,
        .cgi-hazard-shell.cgi-landscape-fallback .cgi-video-frame,
        .cgi-explanation-shell:fullscreen .cgi-explanation-video-frame,
        .cgi-explanation-shell:-webkit-full-screen .cgi-explanation-video-frame,
        .cgi-explanation-shell.cgi-landscape-fallback .cgi-explanation-video-frame {
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

        .cgi-explanation-shell:fullscreen .cgi-timeline-panel,
        .cgi-explanation-shell:-webkit-full-screen .cgi-timeline-panel,
        .cgi-explanation-shell.cgi-landscape-fallback .cgi-timeline-panel {
            flex: 0 0 auto;
            padding-right: max(0.75rem, env(safe-area-inset-right));
            padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
            padding-left: max(0.75rem, env(safe-area-inset-left));
        }
    </style>

    <script>
        function practiceRunner() {
            return {
                initialized: false,
                topic: null,
                items: [],
                currentIndex: 0,
                answers: {},
                hasAnswered: false,
                selectedChoiceId: null,
                showQuestionExplanation: false,
                isSpeaking: false,
                signExplanationVisible: false,
                selectedAdditionalSign: null,
                cgiStage: 'hazard',
                cgiFlags: [],
                cgiFlagSequence: 0,
                cgiScore: 0,
                cgiRangeScores: [],
                cgiInvalidResponse: false,
                cgiInvalidResponseReason: null,
                cgiHazardStarted: false,
                cgiHazardPlayRequested: false,
                cgiHazardLoading: true,
                cgiHazardLoadProgress: 0,
                cgiExplanationTime: 0,
                cgiVideoDuration: 0,
                cgiExplanationLoading: true,
                cgiExplanationLoadProgress: 0,
                cgiExplanationPaused: true,
                cgiScrubbing: false,
                cgiSeekResumePending: false,
                cgiFullscreenFallback: false,
                cgiFullscreenTarget: null,
                languagePreference: 'en',
                languages: [],
                availableAds: [],
                adData: null,
                adPosition: -1,
                showingAd: false,
                adCountdown: 5,
                adTimer: null,
                adShown: false,
                hazardStudy: null,

                initData(topic, items, ads, languages, preferredLanguageCode, hazardStudy) {
                    this.topic = topic;
                    this.items = items;
                    this.languages = languages;
                    this.availableAds = Array.isArray(ads) ? ads : [];
                    this.languagePreference = preferredLanguageCode || 'en';
                    this.hazardStudy = hazardStudy;
                    if (!languages.some(language => language.code === this.languagePreference)) this.languagePreference = 'en';

                    this.items.forEach(item => {
                        if (item.item_type === 'question') {
                            item.media_source = item.media_source || item.media_path || item.media_url || null;
                        }
                    });

                    this.selectAdForLanguage();

                    this.initialized = true;
                    this.restoreItemState();
                },

                get currentItem() {
                    return this.items[this.currentIndex];
                },

                get showTranslation() { return this.languagePreference !== 'en'; },
                get showKurdish() { return this.languagePreference === 'ku'; },
                get languageName() { return this.languages.find(language => language.code === this.languagePreference)?.name || 'Translation'; },
                get languageDirection() { return this.languages.find(language => language.code === this.languagePreference)?.direction || 'ltr'; },
                setLanguage() {
                    localStorage.setItem('languagePreference', this.languagePreference);
                    if (!this.adShown && !this.showingAd) this.selectAdForLanguage();
                },
                selectAdForLanguage() {
                    this.adData = null;
                    this.adPosition = -1;
                    if (this.totalQuestions < 10) return;

                    const languageId = this.languages.find(language => language.code === this.languagePreference)?.id;
                    const matches = this.availableAds.filter(ad => ad.language_id === null || String(ad.language_id) === String(languageId));
                    if (!matches.length) return;

                    const ad = matches[Math.floor(Math.random() * matches.length)];
                    this.adData = { ...ad, media_source: ad.media_path || ad.media_url || null };
                    this.adPosition = 10 + Math.floor(Math.random() * (Math.min(20, this.totalQuestions) - 9));
                },
                translated(item, field) {
                    return item?.translations?.[this.languagePreference]?.[field]
                        || (this.languagePreference === 'ku' ? item?.[`${field}_ku`] : null)
                        || '';
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

                openAdditionalSignModal(image, index) {
                    if (!image) return;

                    this.selectedAdditionalSign = {
                        src: image,
                        index: index + 1,
                    };
                    document.body.style.overflow = 'hidden';
                },

                closeAdditionalSignModal() {
                    if (!this.selectedAdditionalSign) return;

                    this.selectedAdditionalSign = null;
                    document.body.style.removeProperty('overflow');
                },

                get cgiResultMessage() {
                    if (this.cgiInvalidResponse) {
                        return 'The clip was stopped because you clicked continuously or in a repeated pattern. Your score is zero.';
                    }

                    const ratio = this.cgiMaxScore > 0 ? this.cgiScore / this.cgiMaxScore : 0;
                    if (ratio === 1) return 'Excellent — you identified every hazard very early.';
                    if (ratio >= 0.6) return 'Good — you identified the developing hazards.';
                    if (this.cgiScore > 0) return 'You identified the hazard, but a little late.';
                    return 'No hazard was identified inside a scoring window.';
                },

                get cgiHazardRanges() {
                    const ranges = Array.isArray(this.currentItem.hazard_windows)
                        ? this.currentItem.hazard_windows
                            .filter(range => Number(range.end) > Number(range.start))
                            .map(range => {
                                const flagTime = (range.flag_time !== undefined && range.flag_time !== null && range.flag_time !== '')
                                    ? Number(range.flag_time)
                                    : Number(range.start);
                                return { ...range, flag_time: flagTime };
                            })
                        : [];

                    if (ranges.length) return ranges;

                    const legacyStart = Number(this.currentItem.hazard_window_start);
                    const legacyEnd = Number(this.currentItem.hazard_window_end);
                    return legacyEnd > legacyStart ? [{ start: legacyStart, end: legacyEnd, points: 5, flag_time: legacyStart }] : [];
                },

                get cgiMaxScore() {
                    return this.cgiHazardRanges.reduce(
                        (total, range) => total + Math.max(1, Number.parseInt(range.points, 10) || 5),
                        0
                    ) || 5;
                },

                startCgiLoading(target) {
                    if (target === 'hazard') {
                        this.cgiHazardLoading = true;
                        this.cgiHazardLoadProgress = 0;
                        return;
                    }

                    this.cgiExplanationLoading = true;
                    this.cgiExplanationLoadProgress = 0;
                },

                updateCgiLoadProgress(video, target) {
                    if (!video) return;

                    const duration = Number(video.duration);
                    let progress = 0;

                    if (Number.isFinite(duration) && duration > 0 && video.buffered && video.buffered.length) {
                        try {
                            const bufferedEnd = video.buffered.end(video.buffered.length - 1);
                            progress = Math.max(0, Math.min(100, (bufferedEnd / duration) * 100));
                        } catch (error) {
                            progress = 0;
                        }
                    }

                    if (target === 'hazard') {
                        this.cgiHazardLoadProgress = progress;
                    } else {
                        this.cgiExplanationLoadProgress = progress;
                    }
                },

                finishCgiLoading(video, target) {
                    this.updateCgiLoadProgress(video, target);

                    if (target === 'hazard') {
                        this.cgiHazardLoading = false;
                    } else {
                        this.cgiExplanationLoading = false;
                    }
                },

                startCgiHazard() {
                    const video = this.$refs.cgiHazardVideo;
                    if (!video) return;

                    this.cgiHazardPlayRequested = true;
                    this.cgiHazardLoading = video.readyState < 3;
                    video.muted = true;
                    video.volume = 1;
                    const playback = video.play();
                    this.enterCgiFullscreen('hazard');
                    if (playback) {
                        playback.then(() => {
                            this.cgiHazardStarted = true;
                        }).catch(() => {
                            this.cgiHazardPlayRequested = false;
                            this.cgiHazardStarted = false;
                            this.exitCgiFullscreen();
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
                    const previousFlag = this.cgiFlags[this.cgiFlags.length - 1];
                    if (previousFlag && time - previousFlag.time < 0.25) return;

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

                    const invalidReason = this.detectInvalidCgiResponse();
                    if (invalidReason) {
                        this.invalidateCgiResponse(invalidReason, video);
                        return;
                    }

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

                detectInvalidCgiResponse() {
                    const times = this.cgiFlags
                        .map(flag => Number(flag.time))
                        .filter(Number.isFinite)
                        .sort((a, b) => a - b);

                    if (times.length >= 12) {
                        return 'excessive';
                    }

                    // Six clicks inside three seconds is a rapid burst.
                    for (let index = 0; index <= times.length - 6; index++) {
                        if (times[index + 5] - times[index] <= 3) {
                            return 'rapid';
                        }
                    }

                    if (times.length < 6) return null;

                    const intervals = times.slice(1).map((time, index) => time - times[index]);

                    // Five similar intervals catch repeated clicking such as once every second.
                    for (let index = 0; index <= intervals.length - 5; index++) {
                        const sample = intervals.slice(index, index + 5);
                        const average = sample.reduce((total, interval) => total + interval, 0) / sample.length;
                        if (average < 0.5 || average > 3) continue;

                        const tolerance = Math.max(0.2, average * 0.2);
                        if (sample.every(interval => Math.abs(interval - average) <= tolerance)) {
                            return 'pattern';
                        }
                    }

                    return null;
                },

                invalidateCgiResponse(reason, video = null) {
                    const hazardVideo = video || this.$refs.cgiHazardVideo;
                    if (hazardVideo && !hazardVideo.paused) {
                        hazardVideo.pause();
                    }

                    this.cgiInvalidResponseReason = reason;
                    this.cgiInvalidResponse = true;
                    this.cgiScore = 0;
                    this.cgiRangeScores = this.cgiRangeScores.map(() => 0);
                    this.cgiHazardStarted = false;
                    this.exitCgiFullscreen();
                    this.cgiStage = 'result';
                },

                finishCgiHazard() {
                    if (this.cgiStage !== 'hazard') return;

                    const invalidReason = this.detectInvalidCgiResponse();
                    if (invalidReason) {
                        this.invalidateCgiResponse(invalidReason);
                        return;
                    }

                    this.exitCgiFullscreen();
                    this.cgiStage = 'result';
                },

                startCgiExplanation() {
                    this.cgiStage = 'explanation';
                    this.cgiExplanationTime = 0;
                    this.cgiVideoDuration = 0;
                    this.cgiExplanationLoading = true;
                    this.cgiExplanationLoadProgress = 0;
                    this.cgiExplanationPaused = true;
                    this.cgiScrubbing = false;
                    this.$nextTick(() => {
                        const video = this.$refs.cgiExplanationVideo;
                        if (!video) return;

                        if (video.duration && isFinite(video.duration)) {
                            this.cgiVideoDuration = video.duration;
                        }

                        video.muted = false;
                        video.volume = 1;
                        const playback = video.play();
                        this.enterCgiFullscreen('explanation');
                        playback.catch(() => {});
                    });
                },

                syncCgiExplanationVideo(video) {
                    if (!video) return;

                    this.updateCgiLoadProgress(video, 'explanation');

                    if (Number.isFinite(video.duration) && video.duration > 0) {
                        this.cgiVideoDuration = video.duration;
                    }

                    if (!this.cgiScrubbing && Number.isFinite(video.currentTime)) {
                        this.cgiExplanationTime = video.currentTime;
                    }
                },

                toggleCgiExplanationPlayback() {
                    const video = this.$refs.cgiExplanationVideo;
                    if (!video) return;

                    if (video.paused || video.ended) {
                        if (video.ended) {
                            video.currentTime = 0;
                            this.cgiExplanationTime = 0;
                        }

                        this.cgiExplanationLoading = video.readyState < 3;
                        video.play().catch(() => {
                            this.cgiExplanationPaused = true;
                            this.cgiExplanationLoading = false;
                        });
                        return;
                    }

                    video.pause();
                },

                cgiStartScrub() {
                    if (this.cgiScrubbing) return;

                    this.cgiScrubbing = true;
                    const video = this.$refs.cgiExplanationVideo;
                    this._cgiWasPlaying = Boolean(video && !video.paused && !video.ended);
                    this.cgiSeekResumePending = false;

                    if (this._cgiWasPlaying) {
                        video.pause();
                    }
                },

                cgiStopScrub() {
                    if (!this.cgiScrubbing) return;

                    this.cgiScrubbing = false;
                    const video = this.$refs.cgiExplanationVideo;
                    if (!video || !this._cgiWasPlaying) return;

                    if (video.seeking) {
                        this.cgiSeekResumePending = true;
                    } else {
                        video.play().catch(() => {});
                    }
                },

                seekCgiTimeline(value) {
                    if (this.cgiStage !== 'explanation') return;
                    const video = this.$refs.cgiExplanationVideo;
                    if (!video) return;

                    const duration = (video.duration && isFinite(video.duration)) ? video.duration : this.cgiVideoDuration;
                    if (!duration || !isFinite(duration)) return;

                    const newTime = Math.max(0, Math.min(Number(value) || 0, duration));
                    video.currentTime = newTime;
                    this.cgiExplanationTime = newTime;
                },

                finishCgiSeek(video) {
                    this.syncCgiExplanationVideo(video);
                    this.finishCgiLoading(video, 'explanation');

                    if (!this.cgiSeekResumePending) return;
                    this.cgiSeekResumePending = false;
                    video.play().catch(() => {});
                },

                toggleCgiFullscreen(target = 'hazard') {
                    const fullscreenElement = document.fullscreenElement || document.webkitFullscreenElement;
                    if (fullscreenElement || this.cgiFullscreenFallback) {
                        this.exitCgiFullscreen();
                        return;
                    }

                    this.enterCgiFullscreen(target);
                },

                async enterCgiFullscreen(target = 'hazard') {
                    if (this.cgiFullscreenTarget === target) return;

                    const fullscreenElement = document.fullscreenElement || document.webkitFullscreenElement;
                    if (fullscreenElement || this.cgiFullscreenFallback) return;

                    const player = target === 'explanation'
                        ? this.$refs.cgiExplanationPlayer
                        : this.$refs.cgiHazardPlayer;
                    if (!player) return;

                    this.cgiFullscreenTarget = target;
                    const requestFullscreen = player.requestFullscreen
                        ? () => player.requestFullscreen()
                        : (player.webkitRequestFullscreen ? () => player.webkitRequestFullscreen() : null);

                    if (requestFullscreen) {
                        try {
                            const request = requestFullscreen();
                            if (request && typeof request.then === 'function') await request;
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
                    } else if (document.webkitFullscreenElement && document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    }
                    if (screen.orientation && screen.orientation.unlock) {
                        try { screen.orientation.unlock(); } catch (error) {}
                    }
                    this.cgiFullscreenFallback = false;
                    this.cgiFullscreenTarget = null;
                    document.body.style.removeProperty('overflow');
                },

                handleCgiFullscreenChange() {
                    if (document.fullscreenElement || document.webkitFullscreenElement) return;

                    this.cgiFullscreenTarget = null;
                    if (!this.cgiFullscreenFallback) {
                        document.body.style.removeProperty('overflow');
                    }
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

                    return [
                        this.currentItem.explanation_en,
                        this.currentItem.what_to_do_en,
                    ].filter(Boolean).join('. ');
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

                    if (!this.adShown && this.adData && Object.keys(this.answers).length >= this.adPosition) {
                        this.triggerAd();
                    }
                },

                previousItem() {
                    if (this.showingAd || this.currentIndex === 0) return;
                    this.currentIndex--;
                    this.restoreItemState();
                },

                restoreItemState() {
                    if ('speechSynthesis' in window) window.speechSynthesis.cancel();
                    this.isSpeaking = false;
                    const answer = this.currentItem && this.currentItem.item_type === 'question'
                        ? this.answers[this.currentItem.id]
                        : null;
                    this.hasAnswered = Boolean(answer);
                    this.selectedChoiceId = answer ? answer.choiceId : null;
                    this.showQuestionExplanation = false;
                    this.signExplanationVisible = false;
                    this.closeAdditionalSignModal();
                    this.cgiStage = 'hazard';
                    this.cgiFlags = [];
                    this.cgiFlagSequence = 0;
                    this.cgiScore = 0;
                    this.cgiRangeScores = [];
                    this.cgiInvalidResponse = false;
                    this.cgiInvalidResponseReason = null;
                    this.cgiHazardStarted = false;
                    this.cgiHazardPlayRequested = false;
                    this.cgiHazardLoading = true;
                    this.cgiHazardLoadProgress = 0;
                    this.cgiExplanationLoading = true;
                    this.cgiExplanationLoadProgress = 0;
                    this.cgiExplanationPaused = true;
                    this.exitCgiFullscreen();
                },

                triggerAd() {
                    this.showingAd = true;
                    this.adCountdown = 5;
                    this.adShown = true;
                    this.$nextTick(() => {
                        const video = this.$refs.adVideo;
                        if (video) {
                            video.muted = false;
                            video.volume = 1;
                            video.play().catch(() => {});
                        }
                    });
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
                    if (this.hazardStudy) {
                        const watched = JSON.parse(localStorage.getItem('hazardWatched') || '{}');
                        watched[this.hazardStudy.pageId] = new Date().toISOString();
                        localStorage.setItem('hazardWatched', JSON.stringify(watched));
                        window.location.href = this.hazardStudy.returnUrl;
                        return;
                    }
                    if (!this.adShown && this.adData && Object.keys(this.answers).length >= this.adPosition) {
                        this.triggerAd();
                        return;
                    }
                    const reviews = this.items.filter(item => item.item_type === 'question' && this.answers[item.id] && !this.answers[item.id].isCorrect).map(item => ({
                        question: item.text_en,
                        media: item.media_source,
                        selected: item.choices.find(choice => choice.id === this.answers[item.id].choiceId),
                        correct: item.choices.find(choice => choice.is_correct),
                        explanation: item.explanation_en,
                        choices: item.choices.map(choice => ({ ...choice, is_selected: choice.id === this.answers[item.id].choiceId }))
                    }));
                    sessionStorage.setItem('practiceMistakeReview', JSON.stringify(reviews));
                    window.location.href = `{{ route('theory.result') }}?correct=${this.correctCount}&total=${this.totalQuestions}&topic=${encodeURIComponent(this.topic.name_en)}`;
                },

                toggleSpeech() {
                    if (!this.speechText || !('speechSynthesis' in window)) return;
                    if (this.isSpeaking) {
                        window.speechSynthesis.cancel();
                        this.isSpeaking = false;
                        return;
                    }
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(this.speechText);
                    utterance.lang = 'en-GB';
                    utterance.rate = 0.9;
                    utterance.onend = () => this.isSpeaking = false;
                    utterance.onerror = () => this.isSpeaking = false;
                    this.isSpeaking = true;
                    window.speechSynthesis.speak(utterance);
                },

                getYoutubeId(url) {
                    if (!url) return null;
                    const match = url.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/);
                    return match && match[2].length === 11 ? match[2] : null;
                },

                youtubeEmbedUrl(url, autoplay, muted = true) {
                    const id = this.getYoutubeId(url);
                    return id ? `https://www.youtube.com/embed/${id}?autoplay=${autoplay ? 1 : 0}&mute=${muted ? 1 : 0}&playsinline=1` : '';
                },

                isGif(url) {
                    return /\.gif(?:$|[?#])/i.test(url || '');
                },
            };
        }
    </script>
</x-layouts.app>
