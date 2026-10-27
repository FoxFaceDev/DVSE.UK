<?php

use App\Models\Category;
use App\Models\MockTest;
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
            ['text_en' => "Second wrong choice $i", 'is_correct' => false],
            ['text_en' => "Third wrong choice $i", 'is_correct' => false],
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
        $question = Question::create([
            'topic_id' => $topic->id,
            'text_en' => "Non-video question $number",
        ]);
        $question->choices()->createMany([
            ['text_en' => 'Correct answer', 'is_correct' => true],
            ['text_en' => 'Wrong answer one', 'is_correct' => false],
            ['text_en' => 'Wrong answer two', 'is_correct' => false],
            ['text_en' => 'Wrong answer three', 'is_correct' => false],
        ]);
    }

    foreach (range(1, 2) as $number) {
        $question = Question::create([
            'topic_id' => $topic->id,
            'text_en' => "Video question $number",
            'media_type' => 'video',
            'media_url' => "https://example.com/video-$number.mp4",
        ]);
        $question->choices()->createMany([
            ['text_en' => 'Correct answer', 'is_correct' => true],
            ['text_en' => 'Wrong answer one', 'is_correct' => false],
            ['text_en' => 'Wrong answer two', 'is_correct' => false],
            ['text_en' => 'Wrong answer three', 'is_correct' => false],
        ]);
    }

    $response = $this->get(route('theory.mock_test_start', $subSection));

    $response->assertOk()->assertViewHas('questions', function ($questions) {
        expect($questions)->toHaveCount(50);
        expect($questions->take(-2)->pluck('media_type')->all())->toBe(['video', 'video']);

        return true;
    });
});

test('mock tests only include questions with four answers and exit to home', function (bool $dynamic) {
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Mock tests']);
    $category = Category::create(['sub_section_id' => $subSection->id, 'name_en' => 'Rules']);
    $topic = Topic::create(['topicable_type' => Category::class, 'topicable_id' => $category->id, 'name_en' => 'Rules']);
    $eligibleIds = [];

    foreach ([null, 'image', 'video'] as $mediaType) {
        foreach ([3, 4] as $choiceCount) {
            $question = Question::create([
                'topic_id' => $topic->id,
                'text_en' => "Question with $choiceCount answers",
                'media_type' => $mediaType,
            ]);
            foreach (range(1, $choiceCount) as $number) {
                $question->choices()->create([
                    'text_en' => "Answer $number",
                    'is_correct' => $number === 4,
                ]);
            }
            if ($choiceCount === 4) {
                $eligibleIds[] = $question->id;
            }
        }
    }

    if ($dynamic) {
        $mockTest = MockTest::create([
            'sub_section_id' => $subSection->id, 'name' => 'Four answer mock', 'type' => 'theory',
            'question_count' => 3, 'video_question_count' => 1, 'duration_minutes' => 57,
            'pass_mark' => 3, 'is_active' => true,
        ]);
        $mockTest->topics()->attach($topic);
        $url = route('theory.dynamic_mock_start', $mockTest);
    } else {
        $url = route('theory.mock_test_start', $subSection);
    }

    $this->get($url)
        ->assertOk()
        ->assertViewHas('questions', function ($questions) use ($eligibleIds) {
            expect($questions->pluck('id')->sort()->values()->all())->toBe($eligibleIds);
            foreach ($questions as $question) {
                expect($question->choices)->toHaveCount(4);
                expect($question->choices->where('is_correct', true))->toHaveCount(1);
            }

            return true;
        })
        ->assertSee('href="'.route('home').'">Leave test</a>', false)
        ->assertDontSee('data-back', false);
})->with(['standard' => false, 'dynamic' => true]);
