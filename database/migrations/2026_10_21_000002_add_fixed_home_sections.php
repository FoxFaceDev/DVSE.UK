<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->string('home_key', 40)->nullable()->unique()->after('id');
        });

        $cards = [
            'theory' => [
                'name' => 'Theory Test',
                'description' => 'Practise official-style questions and track your progress.',
                'color' => '#1664f5',
                'icon_path' => 'design-v2/Home/img/icon-theory.webp',
                'match' => '%theory%',
            ],
            'find_instructor' => [
                'name' => 'Find an Instructor',
                'description' => 'Search approved driving instructors near you.',
                'color' => '#f7b500',
                'icon_path' => 'design-v2/Home/img/icon-find.webp',
                'match' => '%driving instructor%',
            ],
            'join_rivex' => [
                'name' => 'Instructors — Join RIVEX',
                'description' => 'Get More Students. Earn More Money.',
                'color' => '#f7b500',
                'icon_path' => 'design-v2/Home/img/icon-join.webp',
                'match' => '%join rivex%',
            ],
            'become_instructor' => [
                'name' => 'Become an Instructor',
                'description' => 'Start your journey to become a driving instructor.',
                'color' => '#1664f5',
                'icon_path' => 'design-v2/Home/img/icon-become.webp',
                'match' => '%become%instructor%',
            ],
        ];

        foreach ($cards as $homeKey => $card) {
            $sectionId = DB::table('sections')
                ->whereNull('home_key')
                ->whereRaw('LOWER(name) LIKE ?', [$card['match']])
                ->orderBy('id')
                ->value('id');

            $values = [
                'home_key' => $homeKey,
                'name' => $card['name'],
                'description' => $card['description'],
                'color' => $card['color'],
                'icon_path' => $card['icon_path'],
                'updated_at' => now(),
            ];

            if ($sectionId) {
                DB::table('sections')->where('id', $sectionId)->update($values);
            } else {
                DB::table('sections')->insert($values + ['created_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropUnique(['home_key']);
            $table->dropColumn('home_key');
        });
    }
};
