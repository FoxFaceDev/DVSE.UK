<?php

namespace App\Support;

use App\Models\Category;
use App\Models\SubSection;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RevisionCatalog
{
    public const CARDS = [
        'video-clips' => 'Video Clips',
        'rules-of-the-road' => 'Rules of the Road',
        'safety-margins' => 'Safety Margins',
        'vulnerable-road-users' => 'Vulnerable Road Users',
        'vehicle-handling' => 'Vehicle Handling',
        'motorway-rules' => 'Motorway Rules',
        'incidents-accidents-emergencies' => 'Incidents, Accidents and Emergencies',
        'road-and-traffic-signs' => 'Road and Traffic Signs',
        'hazard-awareness' => 'Hazard Awareness',
        'safety-and-your-vehicle' => 'Safety and Your Vehicle',
        'alertness' => 'Alertness',
        'attitude' => 'Attitude',
        'vehicle-loading' => 'Vehicle Loading',
        'other-types-of-vehicle' => 'Other Types of Vehicle',
        'essential-documents' => 'Essential Documents',
    ];

    public static function icons(): array
    {
        return ['all-categories' => 'All categories'] + self::CARDS;
    }

    public static function connectEntry(Topic $entry): ?Category
    {
        if ($entry->revision_group_id) {
            return $entry->revisionGroup;
        }
        $name = Str::lower($entry->name_en);
        if (! Str::contains($name, 'revision') || ! Str::contains($name, 'question')) {
            return null;
        }
        $parent = $entry->topicable;
        $subSection = $parent instanceof SubSection ? $parent : ($parent instanceof Category ? $parent->subSection : null);
        if (! $subSection) {
            return null;
        }

        return DB::transaction(function () use ($entry, $subSection) {
            $group = $subSection->categories()->where('page_type', 'revision')->orderBy('id')->first();
            if (! $group) {
                $group = $subSection->categories()->create([
                    'name_en' => $entry->name_en,
                    'name_ku' => $entry->name_ku,
                    'page_type' => 'revision',
                ]);
                self::populate($group);
            }

            $entry->revision_group_id = $group->id;
            $entry->save();

            return $group;
        });
    }

    public static function populate(Category $group): void
    {
        foreach (self::CARDS as $icon => $name) {
            $existing = $group->topics()->where('content_type', Topic::TYPE_QUESTIONS)
                ->whereRaw('LOWER(name_en) = ?', [mb_strtolower($name)])->first();
            if ($existing) {
                if (! $existing->icon_preset && ! $existing->getRawOriginal('icon_path')) {
                    $existing->update(['icon_preset' => $icon]);
                }

                continue;
            }

            $group->topics()->create([
                'name_en' => $name,
                'content_type' => Topic::TYPE_QUESTIONS,
                'icon_preset' => $icon,
                'color' => '#1a68e8',
            ]);
        }
    }
}
