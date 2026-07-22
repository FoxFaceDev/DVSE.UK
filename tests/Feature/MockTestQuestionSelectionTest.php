<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\Section;
use App\Models\SubSection;

test('mock tests contain only three video questions and place them last', function () {
    $section = Section::create(['name' => 'Theory Test Practice']);
    $subSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Mock Test Theory',
    ]);
    $category = Category::create([
        'sub_section_id' => $subSection->id,
        'name_en' => 'Mock test questions',
    ]);

    foreach (range(1, 55) as $number) {
        Question::create([
            'category_id' => $category->id,
            'text_en' => "Non-video question $number",
            'media_type' => null,
        ]);
    }

    foreach (range(1, 6) as $number) {
        Question::create([
            'category_id' => $category->id,
            'text_en' => "Video question $number",
            'media_type' => 'video',
            'media_url' => "https://example.com/video-$number.mp4",
        ]);
    }

    $response = $this->get(route('theory.mock_test_start', $subSection));

    $response->assertOk()->assertViewHas('questions', function ($questions) {
        expect($questions)->toHaveCount(50);
        expect($questions->pluck('id')->unique())->toHaveCount(50);
        expect($questions->take(47)->where('media_type', 'video'))->toHaveCount(0);
        expect($questions->take(-3)->pluck('media_type')->all())->toBe([
            'video',
            'video',
            'video',
        ]);

        return true;
    });
});

test('mock tests keep available video questions last when fewer than three exist', function () {
    $section = Section::create(['name' => 'Theory Test Practice']);
    $subSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Mock Test Theory',
    ]);
    $category = Category::create([
        'sub_section_id' => $subSection->id,
        'name_en' => 'Small mock test pool',
    ]);

    foreach (range(1, 48) as $number) {
        Question::create([
            'category_id' => $category->id,
            'text_en' => "Non-video question $number",
        ]);
    }

    foreach (range(1, 2) as $number) {
        Question::create([
            'category_id' => $category->id,
            'text_en' => "Video question $number",
            'media_type' => 'video',
            'media_url' => "https://example.com/video-$number.mp4",
        ]);
    }

    $response = $this->get(route('theory.mock_test_start', $subSection));

    $response->assertOk()->assertViewHas('questions', function ($questions) {
        expect($questions)->toHaveCount(50);
        expect($questions->take(-2)->pluck('media_type')->all())->toBe(['video', 'video']);

        return true;
    });
});
