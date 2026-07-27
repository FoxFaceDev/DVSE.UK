<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;

test('mock tests contain only three video questions and place them last', function () {
    $section = Section::create(['name' => 'Theory Test Practice']);
    $subSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Mock Test Theory',
    ]);
    $category = Category::create([
        'sub_section_id' => $subSection->id,
        'name_en' => 'General Rules',
    ]);

    $topic = Topic::create([
        'topicable_type' => Category::class,
        'topicable_id' => $category->id,
        'name_en' => 'General Rules Topic'
    ]);

    foreach (range(1, 55) as $i) {
        $question = Question::create([
            'topic_id' => $topic->id,
            'text_en' => "Test Question $i",
            'media_type' => $i <= 5 ? 'video' : 'image',
        ]);

        $question->choices()->createMany([
            ['text_en' => "Correct Choice $i", 'is_correct' => true],
            ['text_en' => "Wrong Choice $i", 'is_correct' => false],
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

    $topic = Topic::create([
        'topicable_type' => Category::class,
        'topicable_id' => $category->id,
        'name_en' => 'Small mock test pool topic'
    ]);

    foreach (range(1, 48) as $number) {
        Question::create([
            'topic_id' => $topic->id,
            'text_en' => "Non-video question $number",
        ]);
    }

    foreach (range(1, 2) as $number) {
        Question::create([
            'topic_id' => $topic->id,
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
