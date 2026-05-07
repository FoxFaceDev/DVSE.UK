<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Category;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Main Sections
        $sectionsData = [
            [
                'name' => 'Theory Test Practice',
                'color' => '#3b82f6', // blue-500
                // We'll leave icon_path null for now, admin can upload later
            ],
            [
                'name' => 'Driving Instructors',
                'color' => '#10b981', // emerald-500
            ],
            [
                'name' => 'Life in the UK Test',
                'color' => '#8b5cf6', // violet-500
            ],
            [
                'name' => 'Find The Best Car Insurance',
                'color' => '#f59e0b', // amber-500
            ]
        ];

        $theoryTestSectionId = null;

        foreach ($sectionsData as $data) {
            $section = Section::firstOrCreate(['name' => $data['name']], $data);
            if ($data['name'] === 'Theory Test Practice') {
                $theoryTestSectionId = $section->id;
            }
        }

        // Define SubSections for "Theory Test Practice"
        $subSectionsData = [
            [
                'name' => 'Theory Test Practice',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Hazard Perception',
                'color' => '#ef4444', // red-500
            ],
            [
                'name' => 'Road Signs',
                'color' => '#eab308', // yellow-500
            ],
            [
                'name' => 'Mock Test Theory',
                'color' => '#06b6d4', // cyan-500
            ]
        ];

        $theoryTestSubSectionId = null;

        if ($theoryTestSectionId) {
            foreach ($subSectionsData as $data) {
                $subSection = SubSection::firstOrCreate([
                    'section_id' => $theoryTestSectionId,
                    'name' => $data['name']
                ], $data);

                if ($data['name'] === 'Theory Test Practice') {
                    $theoryTestSubSectionId = $subSection->id;
                }
            }
        }

        // Migrate all existing categories to the "Theory Test Practice" sub-section
        if ($theoryTestSubSectionId) {
            Category::whereNull('sub_section_id')->update(['sub_section_id' => $theoryTestSubSectionId]);
        }
    }
}
