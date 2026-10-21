<?php

namespace App\Http\Controllers;

use App\Models\Section;

class HomeController extends Controller
{
    public function index()
    {
        $sections = Section::homeSections()->get()->keyBy('home_key');

        // Keep locally-created legacy Theory records usable while the fixed slot is
        // introduced; production uses the keyed record migrated from existing data.
        $legacyTheory = Section::whereNull('home_key')->whereRaw('LOWER(name) LIKE ?', ['%theory%'])->latest('id')->first();
        if ($legacyTheory) {
            $sections->put('theory', $legacyTheory);
        }

        $homeCards = collect(Section::HOME_KEYS)->map(function (string $key) use ($sections) {
            $section = $sections->get($key);
            if (! $section) {
                return null;
            }

            return [
                'key' => $key,
                'section' => $section,
                'description' => $section->description ?: match ($key) {
                    'theory' => 'Practise official-style questions and track your progress.',
                    'find_instructor' => 'Search approved driving instructors near you.',
                    'join_rivex' => 'Get More Students. Earn More Money.',
                    'become_instructor' => 'Start your journey to become a driving instructor.',
                },
                'href' => match ($key) {
                    'theory' => route('v2.theory', ['section' => $section->id]),
                    'find_instructor' => route('instructors.index'),
                    'join_rivex' => route('register', ['instructor' => 1]),
                    'become_instructor' => route('v2.guidance'),
                },
                'tone' => in_array($key, ['find_instructor', 'join_rivex'], true) ? 'gold' : 'blue',
            ];
        })->filter()->values();

        $theorySection = $sections->get('theory');

        return view('home', compact('homeCards', 'theorySection'));
    }
}
