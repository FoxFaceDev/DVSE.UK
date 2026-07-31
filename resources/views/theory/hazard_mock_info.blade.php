<x-layouts.app :showBack="true" :backUrl="route('home')" title="Hazard Perception Mock Test">
    <div class="mx-auto max-w-md space-y-6">
        <div class="text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-100 text-purple-800">
                <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.9 2.7 17a2 2 0 0 0 1.73 3h15.14a2 2 0 0 0 1.73-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
                </svg>
            </div>
            <h1 class="font-heading text-2xl font-bold text-primary-dark">Hazard perception mock test</h1>
            <p class="mt-2 text-sm leading-6 text-gray-500">A realistic simulation of the official UK car hazard-perception test.</p>
        </div>

        @if($errors->has('clips'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                {{ $errors->first('clips') }}
            </div>
        @endif

        @if(!$pool['official_ready'])
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <p class="font-bold">Training preview currently available</p>
                <p class="mt-1 leading-6">
                    {{ $pool['eligible_count'] }} complete hazard {{ Str::plural('clip', $pool['eligible_count']) }} available.
                    The score and pass mark will be adjusted to the available hazards.
                </p>
                <p class="mt-2 text-xs leading-5 text-amber-800">
                    The full official-length test activates automatically when the library contains 13 single-hazard clips and one double-hazard clip.
                </p>
            </div>
        @else
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                The complete official-length 14-clip test is available.
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-purple-100 bg-purple-50 p-4">
                <h2 class="flex items-center gap-2 font-bold text-purple-950">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    Test instructions
                </h2>
            </div>
            <ul class="space-y-5 p-6 text-sm text-gray-700">
                <li>
                    <strong class="block text-base text-gray-950">14 clips and 15 hazards</strong>
                    Thirteen clips contain one developing hazard and one clip contains two.
                </li>
                <li>
                    <strong class="block text-base text-gray-950">Pass mark: 44 out of 75</strong>
                    Each developing hazard can score 5, 4, 3, 2, 1 or 0 points.
                </li>
                <li>
                    <strong class="block text-base text-gray-950">One attempt per clip</strong>
                    Clips cannot be paused, replayed, reviewed or scrubbed during the test.
                </li>
                <li>
                    <strong class="block text-base text-gray-950">{{ $timeLimitMinutes }} minute time limit</strong>
                    A countdown runs throughout the test and your responses are submitted automatically when time expires.
                </li>
                <li>
                    <strong class="block text-base text-gray-950">Tap when a hazard develops</strong>
                    Tap anywhere on the video when you would need to change speed or direction.
                </li>
                <li>
                    <strong class="block text-base text-gray-950">Pattern clicking scores zero</strong>
                    Continuous, rapid, rhythmic or excessive clicking stops that clip and gives it zero.
                </li>
            </ul>
        </div>

        @if($pool['eligible_count'] > 0)
            <a href="{{ route('theory.hazard_mock_start') }}" class="block min-h-14 w-full rounded-xl bg-primary px-6 py-4 text-center text-lg font-bold text-white shadow-lg shadow-primary/20 transition hover:bg-primary-dark">
                {{ $pool['official_ready'] ? 'Start hazard mock test' : 'Start training preview' }}
            </a>
        @else
            <button type="button" disabled class="min-h-14 w-full rounded-xl bg-gray-200 px-6 py-4 text-lg font-bold text-gray-500">
                No hazard clips available
            </button>
        @endif
    </div>
</x-layouts.app>
