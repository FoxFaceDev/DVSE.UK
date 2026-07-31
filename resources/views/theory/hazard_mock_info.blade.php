<x-layouts.app
    :showBack="true"
    :backUrl="$hazardSubSection ? route('frontend.sub_section', $hazardSubSection) : route('home')"
    title="Hazard Perception Mock Test"
>
    <div class="mx-auto max-w-md space-y-6">
        <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-dark via-primary to-blue-500 px-6 py-8 text-white shadow-xl shadow-primary/20">
            <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-black/10 blur-2xl"></div>

            <div class="relative z-10">
                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl border border-white/30 bg-white/20 shadow-inner backdrop-blur-sm">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.9 2.7 17a2 2 0 0 0 1.73 3h15.14a2 2 0 0 0 1.73-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
                    </svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-100">Official-style practice</p>
                <h1 class="mt-2 font-heading text-2xl font-bold leading-tight">Car Hazard Perception Test</h1>
                <p class="mt-2 text-sm leading-6 text-white/80">Know what to expect before you begin your mock test.</p>
            </div>
        </header>

        @if($errors->has('clips'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                {{ $errors->first('clips') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-[0_8px_30px_-12px_rgba(15,23,42,0.18)]">
            <div class="p-6">
                <p class="text-sm leading-6 text-gray-700">
                    In this <strong class="font-bold text-gray-950">Car Hazard Perception Test</strong> you will be shown <strong class="font-bold text-gray-950">14 clips.</strong>
                </p>
                <p class="mt-4 text-sm leading-6 text-gray-700">
                    You need to achieve a minimum of <strong class="font-bold text-gray-950">44 out of 75 to pass.</strong>
                </p>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-center">
                        <p class="font-heading text-2xl font-bold text-primary">14</p>
                        <p class="mt-1 text-xs font-bold uppercase tracking-wider text-blue-800">Video clips</p>
                    </div>
                    <div class="rounded-2xl border border-green-100 bg-green-50 p-4 text-center">
                        <p class="font-heading text-2xl font-bold text-green-700">44/75</p>
                        <p class="mt-1 text-xs font-bold uppercase tracking-wider text-green-800">Pass mark</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50/70 p-6">
                <h2 class="font-heading text-base font-bold text-gray-950">As with the official DVSA test, there will be:</h2>

                <ul class="mt-5 space-y-4 text-sm text-gray-700">
                    <li class="flex items-start gap-3">
                        <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-blue-100 text-primary">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m5 12 4 4L19 6"/></svg>
                        </span>
                        <span><strong class="font-bold text-gray-950">One developing hazard</strong> in 13 of the clips</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-blue-100 text-primary">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m5 12 4 4L19 6"/></svg>
                        </span>
                        <span><strong class="font-bold text-gray-950">Two developing hazards</strong> in 1 of the clips</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-blue-100 text-primary">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m5 12 4 4L19 6"/></svg>
                        </span>
                        <span>Each developing hazard has a maximum score of <strong class="font-bold text-gray-950">5 points</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-blue-100 text-primary">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m5 12 4 4L19 6"/></svg>
                        </span>
                        <span>A mock test will take about <strong class="font-bold text-gray-950">{{ $timeLimitMinutes }} minutes</strong></span>
                    </li>
                </ul>
            </div>
        </section>

        <div class="flex items-start gap-3 rounded-2xl border border-purple-100 bg-purple-50 p-4 text-sm text-purple-950">
            <svg class="mt-0.5 h-5 w-5 flex-none text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.9 2.7 17a2 2 0 0 0 1.73 3h15.14a2 2 0 0 0 1.73-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
            </svg>
            <p class="leading-6"><strong>Remember:</strong> tap when a hazard develops. Continuous or patterned clicking will score zero for that clip.</p>
        </div>

        @if($pool['eligible_count'] > 0)
            <a href="{{ route('theory.hazard_mock_start') }}" class="block w-full transform rounded-xl bg-primary py-4 text-center text-lg font-bold text-white shadow-[0_4px_14px_0_rgba(0,118,255,0.39)] transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary-dark">
                Start Hazard Mock Test Now
            </a>
        @else
            <button type="button" disabled class="min-h-14 w-full rounded-xl bg-gray-200 px-6 py-4 text-lg font-bold text-gray-500">
                No hazard clips available
            </button>
        @endif
    </div>
</x-layouts.app>
