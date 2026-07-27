<x-layouts.app :showBack="false" title="Hazard Perception Mock Test">
    <div
        x-data="hazardMockRunner()"
        x-init="initData({{ Js::from($clips) }}, @js($attemptToken))"
        @fullscreenchange.window="handleFullscreenChange()"
        @webkitfullscreenchange.window="handleFullscreenChange()"
        class="mx-auto max-w-md"
    >
        <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-bold text-gray-800">
                Clip <span x-text="currentIndex + 1"></span> / <span x-text="clips.length"></span>
            </div>
            <div class="text-center">
                <p class="text-xs font-bold uppercase tracking-wider text-purple-700">{{ $officialLength ? 'Official-length test' : 'Training preview' }}</p>
                <p class="mt-0.5 text-xs text-gray-500">One attempt per clip</p>
            </div>
            <button type="button" @click="showExitModal = true" class="rounded-full p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-600" aria-label="Exit hazard mock test">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="mb-6 h-1.5 overflow-hidden rounded-full bg-gray-100">
            <div class="h-full rounded-full bg-purple-700 transition-all duration-300" :style="'width:' + (((currentIndex + (status === 'ready' ? 0 : 1)) / clips.length) * 100) + '%'"></div>
        </div>

        <template x-if="status === 'ready'">
            <section class="rounded-2xl border border-purple-100 bg-white p-7 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-purple-100 text-purple-800">
                    <span class="font-heading text-2xl font-bold" x-text="currentIndex + 1"></span>
                </div>
                <h1 class="mt-5 font-heading text-xl font-bold text-gray-950">Get ready for the next clip</h1>
                <p class="mt-2 text-sm leading-6 text-gray-600">Tap the video whenever you see a developing hazard. The clip cannot be paused or replayed.</p>
                <button type="button" @click="startClip()" class="mt-6 min-h-14 w-full rounded-xl bg-purple-800 px-6 py-4 text-lg font-bold text-white transition hover:bg-purple-950">
                    Start clip
                </button>
            </section>
        </template>

        <section
            x-ref="player"
            x-show="status === 'playing'"
            x-cloak
            class="hazard-mock-player overflow-hidden rounded-2xl bg-black shadow-lg"
        >
            <div class="relative aspect-video overflow-hidden bg-black">
                <video
                    x-ref="video"
                    :src="currentClip?.source || ''"
                    playsinline
                    webkit-playsinline
                    preload="auto"
                    disablepictureinpicture
                    muted
                    class="h-full w-full object-contain"
                    @ended="completeClip()"
                    x-on:error="handleVideoError()"
                ></video>
                <button
                    type="button"
                    @click="placeFlag()"
                    class="absolute inset-0 cursor-crosshair touch-manipulation"
                    aria-label="Flag a developing hazard"
                ></button>
            </div>
            <div class="flex min-h-16 items-center gap-1 overflow-x-auto bg-white px-3 py-2" aria-live="polite">
                <span x-show="currentFlags.length === 0" class="text-sm text-gray-400">Your flags will appear here</span>
                <template x-for="(time, index) in currentFlags" :key="index">
                    <svg class="h-10 w-10 flex-none text-red-600" viewBox="0 0 24 24" fill="currentColor" aria-label="Hazard flag">
                        <path d="M5 2.5a1 1 0 0 1 2 0V4h11.2a1 1 0 0 1 .9 1.43L17.4 9l1.7 3.57a1 1 0 0 1-.9 1.43H7v7.5a1 1 0 0 1-2 0v-19Z"/>
                    </svg>
                </template>
                <span class="ml-auto rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700" x-text="currentFlags.length"></span>
            </div>
        </section>

        <template x-if="status === 'complete'">
            <section class="rounded-2xl border border-green-100 bg-white p-7 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-700">
                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/>
                    </svg>
                </div>
                <h2 class="mt-5 font-heading text-xl font-bold text-gray-950">Clip complete</h2>
                <p class="mt-2 text-sm text-gray-600">Your responses have been recorded. Scores remain hidden until the end.</p>
                <button type="button" @click="continueTest()" class="mt-6 min-h-14 w-full rounded-xl bg-primary px-6 py-4 font-bold text-white hover:bg-primary-dark">
                    <span x-text="currentIndex < clips.length - 1 ? 'Continue to next clip' : 'Finish and view result'"></span>
                </button>
            </section>
        </template>

        <template x-if="status === 'invalid'">
            <section class="rounded-2xl border border-red-200 bg-red-50 p-7 text-center shadow-sm" role="alert">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-700">
                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h2 class="mt-5 font-heading text-xl font-bold text-red-950">Clip stopped</h2>
                <p class="mt-2 text-sm leading-6 text-red-800">Continuous or patterned clicking was detected. This clip will score zero.</p>
                <button type="button" @click="continueTest()" class="mt-6 min-h-14 w-full rounded-xl bg-red-700 px-6 py-4 font-bold text-white hover:bg-red-800">
                    <span x-text="currentIndex < clips.length - 1 ? 'Continue to next clip' : 'Finish and view result'"></span>
                </button>
            </section>
        </template>

        <template x-if="status === 'submitting'">
            <section class="rounded-2xl border border-gray-100 bg-white p-10 text-center shadow-sm">
                <div class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-purple-200 border-t-purple-800"></div>
                <p class="mt-4 font-bold text-gray-800">Calculating your result…</p>
            </section>
        </template>

        <template x-if="showExitModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black/70 p-4" style="z-index: 9999">
                <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">
                    <h2 class="font-heading text-xl font-bold text-gray-950">Quit hazard mock test?</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Your current attempt and all recorded responses will be lost.</p>
                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="showExitModal = false" class="flex-1 rounded-xl bg-gray-100 px-4 py-3 font-bold text-gray-800">Cancel</button>
                        <a href="{{ route('home') }}" class="flex-1 rounded-xl bg-red-600 px-4 py-3 font-bold text-white">Quit test</a>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <style>
        .hazard-mock-player:fullscreen,
        .hazard-mock-player:-webkit-full-screen {
            display: flex;
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            flex-direction: column;
            background: #000;
        }

        .hazard-mock-player:fullscreen > div:first-child,
        .hazard-mock-player:-webkit-full-screen > div:first-child {
            min-height: 0;
            flex: 1 1 auto;
        }
    </style>

    <script>
        function hazardMockRunner() {
            return {
                clips: [],
                attemptToken: null,
                currentIndex: 0,
                currentFlags: [],
                responses: [],
                status: 'ready',
                showExitModal: false,

                initData(clips, attemptToken) {
                    this.clips = clips;
                    this.attemptToken = attemptToken;
                    this.$nextTick(() => this.$refs.video?.load());
                },

                get currentClip() {
                    return this.clips[this.currentIndex];
                },

                startClip() {
                    const video = this.$refs.video;
                    if (!video) return;

                    this.currentFlags = [];
                    video.pause();
                    video.currentTime = 0;
                    video.muted = true;
                    this.status = 'playing';
                    const playback = video.play();
                    this.enterFullscreen();
                    playback?.catch(() => {
                        this.status = 'ready';
                        this.exitFullscreen();
                    });
                },

                placeFlag() {
                    if (this.status !== 'playing') return;
                    const video = this.$refs.video;
                    if (!video) return;

                    const time = Number(video.currentTime || 0);
                    const previous = this.currentFlags[this.currentFlags.length - 1];
                    if (previous !== undefined && time - previous < 0.25) return;

                    this.currentFlags.push(time);
                    const invalidReason = this.detectInvalidResponse(this.currentFlags);
                    if (invalidReason) {
                        this.stopInvalidClip(invalidReason);
                    }
                },

                detectInvalidResponse(times) {
                    if (times.length >= 12) return 'excessive';

                    for (let index = 0; index <= times.length - 6; index++) {
                        if (times[index + 5] - times[index] <= 3) return 'rapid';
                    }

                    if (times.length < 6) return null;
                    const intervals = times.slice(1).map((time, index) => time - times[index]);

                    for (let index = 0; index <= intervals.length - 5; index++) {
                        const sample = intervals.slice(index, index + 5);
                        const average = sample.reduce((total, interval) => total + interval, 0) / sample.length;
                        if (average < 0.5 || average > 3) continue;
                        const tolerance = Math.max(0.2, average * 0.2);
                        if (sample.every(interval => Math.abs(interval - average) <= tolerance)) return 'pattern';
                    }

                    return null;
                },

                stopInvalidClip(reason) {
                    const video = this.$refs.video;
                    video?.pause();
                    this.recordResponse(reason);
                    this.exitFullscreen();
                    this.status = 'invalid';
                },

                completeClip() {
                    if (this.status !== 'playing') return;
                    this.recordResponse(null);
                    this.exitFullscreen();
                    this.status = 'complete';
                },

                handleVideoError() {
                    if (this.status !== 'playing') return;
                    this.recordResponse('media');
                    this.exitFullscreen();
                    this.status = 'invalid';
                },

                recordResponse(invalidReason) {
                    this.responses[this.currentIndex] = {
                        content_page_id: this.currentClip.id,
                        flags: [...this.currentFlags],
                        invalid_reason: invalidReason,
                    };
                },

                continueTest() {
                    if (this.currentIndex < this.clips.length - 1) {
                        this.currentIndex++;
                        this.currentFlags = [];
                        this.status = 'ready';
                        this.$nextTick(() => this.$refs.video?.load());
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }

                    this.submitTest();
                },

                submitTest() {
                    this.status = 'submitting';
                    fetch('{{ route('theory.hazard_mock_submit') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            attempt_token: this.attemptToken,
                            responses: this.responses,
                        }),
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Unable to submit hazard mock test.');
                            return response.json();
                        })
                        .then(data => {
                            window.location.href = data.redirect;
                        })
                        .catch(() => {
                            this.status = 'complete';
                        });
                },

                enterFullscreen() {
                    const player = this.$refs.player;
                    const request = player?.requestFullscreen || player?.webkitRequestFullscreen;
                    if (!request) return;
                    try {
                        const result = request.call(player);
                        result?.catch?.(() => {});
                    } catch (error) {}
                },

                exitFullscreen() {
                    if (document.fullscreenElement && document.exitFullscreen) {
                        document.exitFullscreen().catch(() => {});
                    } else if (document.webkitFullscreenElement && document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    }
                },

                handleFullscreenChange() {
                    if (document.fullscreenElement || document.webkitFullscreenElement || this.status !== 'playing') return;
                    // Exiting fullscreen does not pause or reveal controls; the one-attempt clip continues.
                },
            };
        }
    </script>
</x-layouts.app>
