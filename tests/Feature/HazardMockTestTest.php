<?php

use App\Models\Category;
use App\Models\ContentPage;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;

function hazardMockTopic(): Topic
{
    $section = Section::create(['name' => 'Hazard perception']);
    $subSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Developing hazards',
    ]);
    $category = Category::create([
        'sub_section_id' => $subSection->id,
        'name_en' => 'Hazard clips',
    ]);

    return Topic::create([
        'topicable_type' => Category::class,
        'topicable_id' => $category->id,
        'name_en' => 'Hazard perception clips',
    ]);
}

function createHazardMockClip(Topic $topic, array $windows): ContentPage
{
    $page = ContentPage::create([
        'topic_id' => $topic->id,
        'admin_title' => 'Hazard clip',
        'type' => ContentPage::TYPE_CGI_CLIPS,
        'text_en' => 'Review the developing hazard.',
        'text_ku' => 'مەترسییە گەشەسەندووەکە پێداچوونەوە بکە.',
        'hazard_windows' => $windows,
        'hazard_window_start' => $windows[0]['start'],
        'hazard_window_end' => $windows[0]['end'],
    ]);
    $page->clips()->createMany([
        ['slot' => 0, 'media_url' => "https://example.com/hazard-{$page->id}.mp4"],
        ['slot' => 1, 'media_url' => "https://example.com/review-{$page->id}.mp4"],
    ]);

    return $page;
}

test('the hazard perception page links to the hazard mock test instead of the home page', function () {
    $section = Section::create(['name' => 'Theory Test Practice']);
    $hazardSubSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Hazard Perception',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee(route('theory.hazard_mock_info'), false);

    $this->get(route('frontend.sub_section', $hazardSubSection))
        ->assertOk()
        ->assertSee('Hazard Mock Test')
        ->assertSee('Official Mock Test')
        ->assertSee(route('theory.hazard_mock_info'), false);

    $this->get(route('theory.hazard_mock_info'))
        ->assertOk()
        ->assertSee(route('frontend.sub_section', $hazardSubSection), false);
});

test('hazard mock information shows a preview until the official clip pool is ready', function () {
    $topic = hazardMockTopic();
    createHazardMockClip($topic, [
        ['start' => 8, 'end' => 13, 'points' => 5],
    ]);

    $this->get(route('theory.hazard_mock_info'))
        ->assertOk()
        ->assertSee('Training preview currently available')
        ->assertSee('1 complete hazard clip available')
        ->assertSee('In this')
        ->assertSee('Car Hazard Perception Test')
        ->assertSee('44 out of 75 to pass')
        ->assertSee('One developing hazard')
        ->assertSee('Two developing hazards')
        ->assertSee('15 minutes')
        ->assertSee('Start Training Preview');
});

test('an official length attempt contains thirteen single hazard clips and one double hazard clip', function () {
    $topic = hazardMockTopic();

    foreach (range(1, 13) as $number) {
        createHazardMockClip($topic, [
            ['start' => 8, 'end' => 13, 'points' => 5],
        ]);
    }

    createHazardMockClip($topic, [
        ['start' => 8, 'end' => 13, 'points' => 5],
        ['start' => 22, 'end' => 27, 'points' => 5],
    ]);

    $this->get(route('theory.hazard_mock_start'))
        ->assertOk()
        ->assertSee('Official-length test')
        ->assertSee('One attempt per clip')
        ->assertSee('15:00')
        ->assertSee('this.submitTest()', false)
        ->assertSee('detectInvalidResponse(times)', false)
        ->assertSee('x-show="status === \'playing\'"', false)
        ->assertSee('const playback = video.play()', false)
        ->assertDontSee('<template x-if="status === \'playing\'">', false)
        ->assertViewHas('clips', fn ($clips) => count($clips) === 14)
        ->assertSessionHas('hazard_mock_attempt', function ($attempt) {
            return count($attempt['content_page_ids']) === 14
                && is_string($attempt['token'])
                && 15 * 60 === $attempt['expires_at'] - $attempt['started_at'];
        });
});

test('hazard mock scoring is recalculated on the server from click times', function () {
    $topic = hazardMockTopic();
    $page = createHazardMockClip($topic, [
        ['start' => 10, 'end' => 15, 'points' => 5],
    ]);
    $token = 'valid-attempt-token';

    $response = $this
        ->withSession([
            'hazard_mock_attempt' => [
                'token' => $token,
                'content_page_ids' => [$page->id],
                'started_at' => now()->timestamp,
            ],
        ])
        ->postJson(route('theory.hazard_mock_submit'), [
            'attempt_token' => $token,
            'responses' => [[
                'content_page_id' => $page->id,
                'flags' => [10],
            ]],
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('redirect', route('theory.hazard_mock_result'))
        ->assertSessionHas('hazard_mock_result', function ($result) {
            return $result['score'] === 5
                && $result['maximum'] === 5
                && $result['pass_mark'] === 3
                && $result['passed'] === true
                && $result['official_length'] === false;
        });

    $this->get(route('theory.hazard_mock_result'))
        ->assertOk()
        ->assertSee('Test passed')
        ->assertSee('Review your clips')
        ->assertSee('5/5', false)
        ->assertSee('Review the developing hazard.');
});

test('pattern clicking stops a clip and scores zero', function () {
    $topic = hazardMockTopic();
    $page = createHazardMockClip($topic, [
        ['start' => 1, 'end' => 8, 'points' => 5],
    ]);
    $token = 'pattern-attempt-token';

    $this
        ->withSession([
            'hazard_mock_attempt' => [
                'token' => $token,
                'content_page_ids' => [$page->id],
                'started_at' => now()->timestamp,
            ],
        ])
        ->postJson(route('theory.hazard_mock_submit'), [
            'attempt_token' => $token,
            'responses' => [[
                'content_page_id' => $page->id,
                'flags' => [1, 2, 3, 4, 5, 6],
            ]],
        ])
        ->assertOk()
        ->assertSessionHas('hazard_mock_result', function ($result) {
            return $result['score'] === 0
                && $result['passed'] === false
                && $result['reviews'][0]['invalid_reason'] === 'pattern';
        });
});

test('a hazard mock attempt rejects clips that were not selected for it', function () {
    $topic = hazardMockTopic();
    $selected = createHazardMockClip($topic, [
        ['start' => 5, 'end' => 10, 'points' => 5],
    ]);
    $different = createHazardMockClip($topic, [
        ['start' => 12, 'end' => 17, 'points' => 5],
    ]);

    $this
        ->withSession([
            'hazard_mock_attempt' => [
                'token' => 'attempt-token',
                'content_page_ids' => [$selected->id],
            ],
        ])
        ->postJson(route('theory.hazard_mock_submit'), [
            'attempt_token' => 'attempt-token',
            'responses' => [[
                'content_page_id' => $different->id,
                'flags' => [],
            ]],
        ])
        ->assertStatus(422);
});
