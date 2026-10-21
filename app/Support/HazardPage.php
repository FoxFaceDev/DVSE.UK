<?php

namespace App\Support;

use App\Models\Category;
use App\Models\MockTest;
use App\Models\SiteSetting;
use App\Models\SubSection;
use App\Models\Topic;

class HazardPage
{
    public const ICONS = ['guarantee', 'videos', 'clips', 'stopwatch', 'progress', 'info'];

    public static function defaults(): array
    {
        $cards = [];
        foreach ([
            'guarantee' => ['Pass Guarantee', 'guarantee', 'FREE'],
            'help' => ['Help Videos', 'videos', ''],
            'clips' => ['Practise Video Clips', 'clips', ''],
            'mock' => ['Mock Test', 'stopwatch', ''],
            'progress' => ['Progress Monitor', 'progress', ''],
            'advice' => ['Advice and Information', 'info', ''],
        ] as $key => [$label, $icon, $badge]) {
            $cards[$key] = compact('label', 'icon', 'badge') + [
                'destination' => $key,
                'position' => count($cards) + 1,
                'active' => true,
                'color' => '#eef3fb',
            ];
        }

        return [
            'title' => 'Hazard Perception',
            'banner_active' => true,
            'banner_destination' => 'instructors',
            'banner' => [
                'en' => ['before' => 'Find a Driving', 'highlight' => 'Instructor', 'after' => 'Near You', 'button' => 'Find an Instructor'],
                'ku' => ['before' => 'دۆزینەوەی', 'highlight' => 'مامۆستای لێخوڕین', 'after' => 'لە نزیک خۆت', 'button' => 'دۆزینەوەی مامۆستا'],
            ],
            'cards' => $cards,
        ];
    }

    public static function settings(): array
    {
        return array_replace_recursive(self::defaults(), SiteSetting::json('hazard_page'));
    }

    public static function destinations(?SubSection $subSection = null): array
    {
        $context = $subSection ? ['subsection' => $subSection->id] : [];
        $destinations = [
            'instructors' => ['label' => 'Instructor directory', 'url' => route('instructors.index')],
            'guarantee' => ['label' => 'Pass Guarantee', 'url' => route('v2.guarantee')],
            'help' => ['label' => 'Help video library', 'url' => route('v2.hazards', $context + ['clips' => 1, 'mode' => 'help'])],
            'clips' => ['label' => 'Hazard practice clips', 'url' => route('v2.hazards', $context + ['clips' => 1])],
            'mock' => ['label' => 'Hazard mock test', 'url' => route('theory.hazard_mock_info')],
            'progress' => ['label' => 'Learning progress', 'url' => route('v2.progress')],
            'advice' => ['label' => 'Driving guidance', 'url' => route('v2.guidance')],
        ];

        foreach (Category::orderBy('name_en')->get() as $category) {
            $destinations['category:'.$category->id] = ['label' => 'Category group: '.$category->name_en, 'url' => route('frontend.category', $category)];
        }
        foreach (Topic::withCount(['contentPages as cgi_count' => fn ($query) => $query->where('type', 'cgi_clips')])->orderBy('name_en')->get() as $topic) {
            $destinations['topic:'.$topic->id] = [
                'label' => 'Category: '.$topic->name_en,
                'url' => $topic->isLearningCategory() && $topic->cgi_count ? route('theory.hazard_library', $topic) : route('theory.practice', $topic),
            ];
        }
        foreach (MockTest::where('is_active', true)->orderBy('name')->get() as $mock) {
            $destinations['mock:'.$mock->id] = ['label' => 'Mock test: '.$mock->name, 'url' => $mock->type === 'hazard' ? route('theory.hazard_mock_info') : route('theory.dynamic_mock_info', $mock)];
        }

        return $destinations;
    }

    public static function viewData(?SubSection $subSection, string $language): array
    {
        $settings = self::settings();
        $destinations = self::destinations($subSection);
        $cards = collect($settings['cards'])->filter(fn ($card) => $card['active'] && isset($destinations[$card['destination']]))
            ->sortBy('position')->map(fn ($card) => $card + ['url' => $destinations[$card['destination']]['url']]);

        return [
            'hazardSettings' => $settings,
            'hazardCards' => $cards,
            'banner' => $settings['banner'][$language],
            'bannerUrl' => $destinations[$settings['banner_destination']]['url'] ?? route('instructors.index'),
            'bannerLanguage' => $language,
            'subSection' => $subSection,
        ];
    }
}
