<?php

namespace App\Services;

use App\Models\ContentPage;
use Illuminate\Support\Collection;

class HazardMockTestService
{
    public const OFFICIAL_CLIP_COUNT = 14;

    public const OFFICIAL_HAZARD_COUNT = 15;

    public const OFFICIAL_MAX_SCORE = 75;

    public const OFFICIAL_PASS_MARK = 44;

    public function eligibleClips(): Collection
    {
        return ContentPage::query()
            ->where('type', ContentPage::TYPE_CGI_CLIPS)
            ->whereHas('clips', fn ($query) => $query
                ->where('slot', 0)
                ->where(function ($mediaQuery) {
                    $mediaQuery->whereNotNull('media_path')->orWhereNotNull('media_url');
                }))
            ->with('clips')
            ->get()
            ->filter(function (ContentPage $page) {
                $hazardCount = count($this->rangesFor($page));

                return $hazardCount === 1 || $hazardCount === 2;
            })
            ->values();
    }

    public function selectClips(): Collection
    {
        $eligible = $this->eligibleClips();
        $singleHazard = $eligible
            ->filter(fn (ContentPage $page) => count($this->rangesFor($page)) === 1)
            ->shuffle()
            ->values();
        $doubleHazard = $eligible
            ->filter(fn (ContentPage $page) => count($this->rangesFor($page)) === 2)
            ->shuffle()
            ->values();

        if ($singleHazard->count() >= 13 && $doubleHazard->isNotEmpty()) {
            return $singleHazard
                ->take(13)
                ->push($doubleHazard->first())
                ->shuffle()
                ->values();
        }

        return $eligible->shuffle()->take(self::OFFICIAL_CLIP_COUNT)->values();
    }

    public function poolStatus(): array
    {
        $eligible = $this->eligibleClips();
        $singleCount = $eligible->filter(fn ($page) => count($this->rangesFor($page)) === 1)->count();
        $doubleCount = $eligible->filter(fn ($page) => count($this->rangesFor($page)) === 2)->count();

        return [
            'eligible_count' => $eligible->count(),
            'single_count' => $singleCount,
            'double_count' => $doubleCount,
            'official_ready' => $singleCount >= 13 && $doubleCount >= 1,
            'single_needed' => max(0, 13 - $singleCount),
            'double_needed' => max(0, 1 - $doubleCount),
        ];
    }

    public function startPayload(Collection $clips): array
    {
        return $clips->map(function (ContentPage $page) {
            $hazardClip = $page->clips->firstWhere('slot', 0);

            return [
                'id' => $page->id,
                'source' => $hazardClip?->source,
            ];
        })->values()->all();
    }

    public function scoreAttempt(Collection $clips, array $responses): array
    {
        $responsesByPage = collect($responses)->keyBy(fn ($response) => (int) ($response['content_page_id'] ?? 0));
        $reviews = [];
        $totalScore = 0;
        $maximumScore = 0;
        $hazardCount = 0;

        foreach ($clips->values() as $index => $page) {
            $ranges = $this->rangesFor($page);
            $response = $responsesByPage->get($page->id, []);
            $flags = $this->normaliseFlags($response['flags'] ?? []);
            $invalidReason = $this->detectInvalidResponse($flags);
            $maximum = count($ranges) * 5;
            $score = $invalidReason ? 0 : $this->scoreRanges($flags, $ranges);
            $explanationClip = $page->clips->firstWhere('slot', 1);
            $hazardClip = $page->clips->firstWhere('slot', 0);

            $maximumScore += $maximum;
            $totalScore += $score;
            $hazardCount += count($ranges);

            $reviews[] = [
                'number' => $index + 1,
                'content_page_id' => $page->id,
                'title' => $page->admin_title ?: 'Hazard clip '.($index + 1),
                'source' => $explanationClip?->source ?: $hazardClip?->source,
                'text_en' => $page->text_en,
                'text_ku' => $page->text_ku,
                'ranges' => $ranges,
                'flags' => $flags,
                'score' => $score,
                'maximum' => $maximum,
                'invalid_reason' => $invalidReason,
            ];
        }

        $isOfficialLength = $clips->count() === self::OFFICIAL_CLIP_COUNT
            && $hazardCount === self::OFFICIAL_HAZARD_COUNT
            && $maximumScore === self::OFFICIAL_MAX_SCORE;
        $passMark = $isOfficialLength
            ? self::OFFICIAL_PASS_MARK
            : max(1, (int) ceil($maximumScore * self::OFFICIAL_PASS_MARK / self::OFFICIAL_MAX_SCORE));

        return [
            'score' => $totalScore,
            'maximum' => $maximumScore,
            'pass_mark' => $passMark,
            'passed' => $totalScore >= $passMark,
            'clip_count' => $clips->count(),
            'hazard_count' => $hazardCount,
            'official_length' => $isOfficialLength,
            'reviews' => $reviews,
        ];
    }

    public function rangesFor(ContentPage $page): array
    {
        $ranges = collect(is_array($page->hazard_windows) ? $page->hazard_windows : [])
            ->filter(fn ($range) => is_numeric($range['start'] ?? null)
                && is_numeric($range['end'] ?? null)
                && (float) $range['end'] > (float) $range['start'])
            ->map(fn ($range) => [
                'start' => (float) $range['start'],
                'end' => (float) $range['end'],
                'points' => 5,
            ])
            ->values()
            ->all();

        if ($ranges) {
            return $ranges;
        }

        $start = (float) $page->hazard_window_start;
        $end = (float) $page->hazard_window_end;

        return $end > $start
            ? [['start' => $start, 'end' => $end, 'points' => 5]]
            : [];
    }

    public function detectInvalidResponse(array $times): ?string
    {
        if (count($times) >= 12) {
            return 'excessive';
        }

        for ($index = 0; $index <= count($times) - 6; $index++) {
            if ($times[$index + 5] - $times[$index] <= 3) {
                return 'rapid';
            }
        }

        if (count($times) < 6) {
            return null;
        }

        $intervals = [];
        for ($index = 1; $index < count($times); $index++) {
            $intervals[] = $times[$index] - $times[$index - 1];
        }

        for ($index = 0; $index <= count($intervals) - 5; $index++) {
            $sample = array_slice($intervals, $index, 5);
            $average = array_sum($sample) / count($sample);
            if ($average < 0.5 || $average > 3) {
                continue;
            }

            $tolerance = max(0.2, $average * 0.2);
            $isPattern = collect($sample)
                ->every(fn ($interval) => abs($interval - $average) <= $tolerance);

            if ($isPattern) {
                return 'pattern';
            }
        }

        return null;
    }

    private function normaliseFlags(array $flags): array
    {
        $times = collect($flags)
            ->filter(fn ($time) => is_numeric($time) && (float) $time >= 0)
            ->map(fn ($time) => round((float) $time, 3))
            ->sort()
            ->values();
        $normalised = [];

        foreach ($times as $time) {
            $previous = $normalised[count($normalised) - 1] ?? null;
            if ($previous !== null && $time - $previous < 0.25) {
                continue;
            }

            $normalised[] = $time;
        }

        return $normalised;
    }

    private function scoreRanges(array $flags, array $ranges): int
    {
        return collect($ranges)->sum(function ($range) use ($flags) {
            return collect($flags)
                ->map(fn ($time) => $this->scoreTimeForRange($time, $range))
                ->max() ?? 0;
        });
    }

    private function scoreTimeForRange(float $time, array $range): int
    {
        $start = (float) $range['start'];
        $end = (float) $range['end'];

        if ($time < $start || $time > $end || $end <= $start) {
            return 0;
        }

        $zone = min(4, (int) floor((($time - $start) / ($end - $start)) * 5));

        return 5 - $zone;
    }
}
