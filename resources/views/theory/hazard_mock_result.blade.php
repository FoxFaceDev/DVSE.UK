<x-layouts.app :showBack="true" :backUrl="route('home')" title="Hazard Perception Result">
    <div class="mx-auto max-w-md space-y-6">
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="{{ $result['passed'] ? 'bg-green-600' : 'bg-red-600' }} px-6 py-9 text-center text-white">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/20">
                    @if($result['passed'])
                        <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/>
                        </svg>
                    @else
                        <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    @endif
                </div>
                <h1 class="mt-4 font-heading text-3xl font-bold">{{ $result['passed'] ? 'Test passed' : 'Test failed' }}</h1>
                <p class="mt-1 text-sm text-white/90">
                    {{ $result['official_length'] ? 'Official-length hazard perception mock test' : 'Hazard perception training preview' }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-px bg-gray-200">
                <div class="bg-white p-5 text-center">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Your score</p>
                    <p class="mt-1 text-4xl font-black {{ $result['passed'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $result['score'] }}<span class="text-xl text-gray-400">/{{ $result['maximum'] }}</span>
                    </p>
                </div>
                <div class="bg-white p-5 text-center">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pass mark</p>
                    <p class="mt-1 text-4xl font-black text-gray-800">{{ $result['pass_mark'] }}</p>
                </div>
            </div>

            <div class="p-5 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Clips completed</span>
                    <strong class="text-gray-950">{{ $result['clip_count'] }}</strong>
                </div>
                <div class="mt-3 flex justify-between">
                    <span>Developing hazards</span>
                    <strong class="text-gray-950">{{ $result['hazard_count'] }}</strong>
                </div>
                @if(!$result['official_length'])
                    <p class="mt-4 rounded-xl bg-amber-50 p-3 text-xs leading-5 text-amber-800">
                        This preview used the available clip library, so its maximum score and pass mark were adjusted proportionally.
                    </p>
                @endif
            </div>
        </section>

        <section>
            <h2 class="font-heading text-xl font-bold text-gray-950">Review your clips</h2>
            <p class="mt-1 text-sm text-gray-500">Scores and explanations are available only after completing the test.</p>

            <div class="mt-4 space-y-3">
                @foreach($result['reviews'] as $review)
                    <details class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4">
                            <div>
                                <p class="font-bold text-gray-950">Clip {{ $review['number'] }}</p>
                                @if($review['invalid_reason'])
                                    <p class="mt-0.5 text-xs font-medium text-red-600">Pattern clicking detected — zero points</p>
                                @else
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $review['title'] }}</p>
                                @endif
                            </div>
                            <div class="rounded-full px-3 py-1 text-sm font-bold {{ $review['invalid_reason'] ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-800' }}">
                                {{ $review['score'] }}/{{ $review['maximum'] }}
                            </div>
                        </summary>

                        <div
                            x-data="{
                                time: 0,
                                duration: 0,
                                ranges: {{ Js::from($review['ranges']) }},
                                flags: {{ Js::from($review['flags']) }}
                            }"
                            class="border-t border-gray-100 p-4"
                        >
                            <video
                                src="{{ $review['source'] }}"
                                controls
                                playsinline
                                preload="metadata"
                                class="aspect-video w-full rounded-lg bg-black object-contain"
                                @loadedmetadata="duration = $event.target.duration"
                                @durationchange="duration = $event.target.duration"
                                @timeupdate="time = $event.target.currentTime"
                            ></video>

                            <div x-show="duration > 0" x-cloak class="relative mt-3 h-12 overflow-visible rounded-lg border border-gray-200 bg-gray-100">
                                <div class="pointer-events-none absolute inset-y-0 z-30 w-0.5 bg-black" :style="'left:' + ((time / duration) * 100) + '%'">
                                    <div class="absolute -left-1.5 -top-1.5 h-3.5 w-3.5 rounded-full bg-black"></div>
                                </div>

                                <template x-for="(range, rangeIndex) in ranges" :key="'range-' + rangeIndex">
                                    <div class="absolute inset-y-0 z-10 flex overflow-hidden"
                                         :style="'left:' + ((range.start / duration) * 100) + '%; width:' + (((range.end - range.start) / duration) * 100) + '%'">
                                        <template x-for="segment in 5" :key="segment">
                                            <div class="flex flex-1 items-center justify-center border-r border-white/30 text-xs font-bold text-white"
                                                 :style="'background:' + ['#b91c1c','#dc2626','#ef4444','#f87171','#fca5a5'][segment - 1]">
                                                <span x-text="6 - segment"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-for="(flag, flagIndex) in flags" :key="'flag-' + flagIndex">
                                    <div x-show="time >= flag" class="pointer-events-none absolute z-20 -translate-x-1/2" :style="'left:' + ((flag / duration) * 100) + '%; top:-8px'">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="#dc2626" aria-label="Your hazard flag">
                                            <path d="M5 2.5a1 1 0 0 1 2 0V4h11.2a1 1 0 0 1 .9 1.43L17.4 9l1.7 3.57a1 1 0 0 1-.9 1.43H7v7.5a1 1 0 0 1-2 0v-19Z"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>

                            @if($review['text_en'])
                                <p class="mt-4 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $review['text_en'] }}</p>
                            @endif
                            @if($review['text_ku'])
                                <p class="mt-3 border-t border-gray-100 pt-3 text-right text-sm leading-6 text-gray-700" dir="rtl">{{ $review['text_ku'] }}</p>
                            @endif
                        </div>
                    </details>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('home') }}" class="rounded-xl bg-gray-100 px-4 py-4 text-center font-bold text-gray-800 hover:bg-gray-200">Back to home</a>
            <a href="{{ route('theory.hazard_mock_info') }}" class="rounded-xl bg-primary px-4 py-4 text-center font-bold text-white hover:bg-primary-dark">Try again</a>
        </div>
    </div>
</x-layouts.app>
