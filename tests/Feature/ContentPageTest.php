<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\ContentPage;
use App\Models\Question;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function learningPageAdmin(): Admin
{
    return Admin::create([
        'name' => 'Content Admin',
        'email' => 'content-admin@example.com',
        'password' => bcrypt('password'),
    ]);
}

function learningPageTopic(): Topic
{
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Road knowledge',
    ]);

    $category = Category::create([
        'sub_section_id' => $subSection->id,
        'name_en' => 'Motorways',
        'name_ku' => null,
    ]);

    return Topic::create([
        'topicable_type' => Category::class,
        'topicable_id' => $category->id,
        'name_en' => 'Motorways Topic',
    ]);
}

test('an admin can create a CGI page with hazard and explanation videos', function () {
    Storage::fake('public');
    $topic = learningPageTopic();

    $response = $this->actingAs(learningPageAdmin(), 'admin')->post(route('admin.content-pages.store'), [
        'topic_id' => $topic->id,
        'admin_title' => 'Vehicles approaching a bend',
        'type' => ContentPage::TYPE_CGI_CLIPS,
        'library_category' => 'Rural roads',
        'text_en' => 'Compare how the vehicles move through the bend.',
        'hazard_windows' => [
            ['start' => 8.5, 'end' => 13.5, 'points' => 5],
            ['start' => 22, 'end' => 27, 'points' => 8],
        ],
        'clips' => [
            0 => ['media' => UploadedFile::fake()->create('hazard.mp4', 100, 'video/mp4')],
            1 => ['media' => UploadedFile::fake()->create('explanation.mp4', 100, 'video/mp4')],
        ],
    ]);

    $response->assertRedirect(route('admin.content-pages.index'))->assertSessionHasNoErrors();
    $this->assertDatabaseHas('content_pages', [
        'topic_id' => $topic->id,
        'admin_title' => 'Vehicles approaching a bend',
        'type' => ContentPage::TYPE_CGI_CLIPS,
        'library_category' => 'Rural roads',
        'text_en' => 'Compare how the vehicles move through the bend.',
        'hazard_window_start' => 8.5,
        'hazard_window_end' => 13.5,
    ]);
    $page = ContentPage::where('type', ContentPage::TYPE_CGI_CLIPS)->firstOrFail();
    expect($page->hazard_windows)->toBe([
        ['start' => 8.5, 'end' => 13.5, 'points' => 5, 'flag_time' => 8.5],
        ['start' => 22, 'end' => 27, 'points' => 8, 'flag_time' => 22],
    ]);
    $this->assertDatabaseHas('cgi_clips', [
        'slot' => 0,
        'media_url' => null,
    ]);
    $this->assertDatabaseHas('cgi_clips', ['slot' => 1, 'media_url' => null]);
    $this->get(route('admin.content-pages.index'))->assertOk()->assertSee('CGI clips');
    $this->get(route('admin.content-pages.create'))
        ->assertOk()
        ->assertSee('<option value="Rural roads">Rural roads</option>', false)
        ->assertSee('+ Add a new category')
        ->assertSee('Add another hazard range')
        ->assertSee('Max points');
});

test('a CGI page requires at least one remaining clip', function () {
    $topic = learningPageTopic();
    $page = ContentPage::create([
        'topic_id' => $topic->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
    ]);
    $clip = $page->clips()->create([
        'slot' => 0,
        'media_url' => 'https://example.com/existing.mp4',
    ]);

    $response = $this->actingAs(learningPageAdmin(), 'admin')->put(route('admin.content-pages.update', $page), [
        'topic_id' => $topic->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
        'hazard_windows' => [
            ['start' => 8.5, 'end' => 13.5, 'points' => 5],
        ],
        'clips' => [
            0 => ['remove' => '1'],
        ],
    ]);

    $response->assertSessionHasErrors('clips');
    expect($clip->fresh())->not->toBeNull();
});

test('a CGI page requires a valid hazard scoring window', function () {
    Storage::fake('public');
    $topic = learningPageTopic();

    $response = $this->actingAs(learningPageAdmin(), 'admin')->post(route('admin.content-pages.store'), [
        'topic_id' => $topic->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
        'hazard_windows' => [
            ['start' => 12, 'end' => 8, 'points' => 5],
        ],
        'clips' => [
            0 => ['media' => UploadedFile::fake()->create('hazard.mp4', 100, 'video/mp4')],
            1 => ['media' => UploadedFile::fake()->create('explanation.mp4', 100, 'video/mp4')],
        ],
    ]);

    $response->assertSessionHasErrors('hazard_windows.0.end');
});

test('each CGI hazard range requires a positive point value', function () {
    Storage::fake('public');
    $topic = learningPageTopic();

    $response = $this->actingAs(learningPageAdmin(), 'admin')->post(route('admin.content-pages.store'), [
        'topic_id' => $topic->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
        'hazard_windows' => [
            ['start' => 8, 'end' => 12, 'points' => 0],
        ],
        'clips' => [
            0 => ['media' => UploadedFile::fake()->create('hazard.mp4', 100, 'video/mp4')],
            1 => ['media' => UploadedFile::fake()->create('explanation.mp4', 100, 'video/mp4')],
        ],
    ]);

    $response->assertSessionHasErrors('hazard_windows.0.points');
});

test('an admin can create a motorway sign page with an explanation', function () {
    Storage::fake('public');
    $topic = learningPageTopic();

    $response = $this->actingAs(learningPageAdmin(), 'admin')->post(route('admin.content-pages.store'), [
        'topic_id' => $topic->id,
        'admin_title' => 'Motorway regulations begin',
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
        'sign_image' => UploadedFile::fake()->image('motorway-sign.png', 600, 600),
        'explanation_en' => 'This sign marks the beginning of motorway regulations.',
        'what_to_do_en' => 'Follow motorway regulations from this point.',
        'additional_sign_images' => [
            UploadedFile::fake()->image('related-sign.png', 200, 200),
        ],
    ]);

    $response->assertRedirect(route('admin.content-pages.index'))->assertSessionHasNoErrors();
    $page = ContentPage::where('type', ContentPage::TYPE_MOTORWAY_SIGN)->firstOrFail();

    expect($page->explanation_en)->toBe('This sign marks the beginning of motorway regulations.');
    expect($page->what_to_do_en)->toBe('Follow motorway regulations from this point.')
        ->and($page->additional_sign_images)->toHaveCount(1);
    Storage::disk('public')->assertExists(str_replace('/storage/', '', $page->getRawOriginal('sign_image_path')));
    Storage::disk('public')->assertExists(str_replace('/storage/', '', $page->additional_sign_images[0]));
});

test('a motorway sign page requires both an image and an explanation', function () {
    $topic = learningPageTopic();

    $response = $this->actingAs(learningPageAdmin(), 'admin')->post(route('admin.content-pages.store'), [
        'topic_id' => $topic->id,
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
    ]);

    $response->assertSessionHasErrors(['sign_image', 'explanation_en', 'what_to_do_en']);
});

test('an admin can update a motorway sign when hidden hazard fields are empty', function () {
    $topic = learningPageTopic();
    $page = ContentPage::create([
        'topic_id' => $topic->id,
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
        'sign_image_path' => '/storage/content-pages/signs/example.png',
        'explanation_en' => 'Original information about this sign.',
        'what_to_do_en' => 'Original learner guidance.',
    ]);

    $response = $this->actingAs(learningPageAdmin(), 'admin')->put(route('admin.content-pages.update', $page), [
        'topic_id' => $topic->id,
        'admin_title' => 'Updated motorway sign',
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
        'explanation_en' => 'Updated information about this sign.',
        'what_to_do_en' => 'Updated learner guidance.',
        // These blank inputs are present in the shared form but belong only to CGI pages.
        'hazard_windows' => [
            ['start' => '', 'end' => '', 'points' => ''],
        ],
    ]);

    $response->assertRedirect(route('admin.content-pages.index'))->assertSessionHasNoErrors();
    expect($page->fresh())
        ->explanation_en->toBe('Updated information about this sign.')
        ->what_to_do_en->toBe('Updated learner guidance.');
});

test('an admin can search learning pages by title content category or id', function () {
    $topic = learningPageTopic();
    $admin = learningPageAdmin();
    $matchingPage = ContentPage::create([
        'topic_id' => $topic->id,
        'admin_title' => 'Temporary waiting restriction',
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
        'sign_image_path' => '/storage/content-pages/signs/waiting.png',
        'explanation_en' => 'A yellow board with a red order circle.',
        'what_to_do_en' => 'Do not wait here.',
    ]);
    ContentPage::create([
        'topic_id' => $topic->id,
        'admin_title' => 'Beginning of motorway',
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
        'sign_image_path' => '/storage/content-pages/signs/motorway.png',
        'explanation_en' => 'Motorway rules apply.',
        'what_to_do_en' => 'Follow motorway rules.',
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.content-pages.index', ['q' => 'waiting']))
        ->assertOk()
        ->assertSee('Temporary waiting restriction')
        ->assertDontSee('Beginning of motorway');

    $this->get(route('admin.content-pages.index', ['q' => (string) $matchingPage->id]))
        ->assertOk()
        ->assertSee('Temporary waiting restriction');
});

test('learning page results are paginated and keep filters', function () {
    $topic = learningPageTopic();

    foreach (range(1, 16) as $number) {
        ContentPage::create([
            'topic_id' => $topic->id,
            'admin_title' => "Sign page {$number}",
            'type' => ContentPage::TYPE_MOTORWAY_SIGN,
            'sign_image_path' => "/storage/content-pages/signs/{$number}.png",
            'explanation_en' => "Explanation {$number}",
            'what_to_do_en' => "Guidance {$number}",
        ]);
    }

    $this->actingAs(learningPageAdmin(), 'admin')
        ->get(route('admin.content-pages.index', [
            'type' => ContentPage::TYPE_MOTORWAY_SIGN,
            'sort' => 'oldest',
        ]))
        ->assertOk()
        ->assertSee('Sign page 1')
        ->assertDontSee('Sign page 16')
        ->assertSee('page=2', false)
        ->assertSee('type=motorway_sign', false);
});

test('practice contains questions CGI pages and motorway sign pages', function () {
    $topic = learningPageTopic();
    $question = Question::create([
        'topic_id' => $topic->id,
        'text_en' => 'What should you do?',
    ]);
    $question->choices()->createMany([
        ['text_en' => 'Slow down', 'is_correct' => true],
        ['text_en' => 'Speed up', 'is_correct' => false],
        ['text_en' => 'Stop immediately', 'is_correct' => false],
        ['text_en' => 'Sound the horn', 'is_correct' => false],
    ]);

    $cgiPage = ContentPage::create([
        'topic_id' => $topic->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
    ]);
    $cgiPage->clips()->create([
        'slot' => 0,
        'media_url' => 'https://example.com/cgi.mp4',
    ]);
    ContentPage::create([
        'topic_id' => $topic->id,
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
        'sign_image_path' => '/storage/content-pages/signs/example.png',
        'explanation_en' => 'Motorway regulations begin here.',
    ]);

    $response = $this->get(route('theory.practice', $topic));

    $response
        ->assertOk()
        ->assertSee('Start hazard clip')
        ->assertSee('cgi-flag-strip', false)
        ->assertSee('toggleCgiFullscreen()', false)
        ->assertSee("toggleCgiFullscreen('explanation')", false)
        ->assertSee("@play=\"enterCgiFullscreen('hazard')\"", false)
        ->assertSee("enterCgiFullscreen('explanation')", false)
        ->assertSee(':key="\'review-flag-\' + flag.id"', false)
        ->assertSee('x-if="cgiExplanationTime >= flag.time"', false)
        ->assertSee('(flag.time / cgiVideoDuration * 100)', false)
        ->assertDontSee('cgiHazardTimelineMarkers', false)
        ->assertDontSee('x-show="cgiExplanationTime >= range.flag_time"', false)
        ->assertSee('Replay only the flags the learner placed during the hazard video.', false)
        ->assertSee('detectInvalidCgiResponse()', false)
        ->assertSee('times[index + 5] - times[index] <= 3', false)
        ->assertSee('times.length >= 12', false)
        ->assertSee('time - previousFlag.time < 0.25', false)
        ->assertSee('this.invalidateCgiResponse(invalidReason, video)', false)
        ->assertSee('hazardVideo.pause()', false)
        ->assertSee("this.cgiStage = 'result'", false)
        ->assertSee('The clip was stopped because you clicked continuously or in a repeated pattern. Your score is zero.')
        ->assertSee('Explanation video timeline')
        ->assertSee('ڕوونکردنەوە')
        ->assertSee('openAdditionalSignModal(image, index)', false)
        ->assertSee('aria-modal="true"', false)
        ->assertSee('style="z-index: 9999"', false)
        ->assertDontSee('x-ref="additionalSignModalClose"', false)
        ->assertSee('نیشانە زیادەکان کە لەوانەیە ببینیت')
        ->assertDontSee('Additional road sign <span x-text="selectedAdditionalSign.index"', false)
        ->assertDontSee('نیشانەی زیادەی ڕێگا')
        ->assertSee('Tap to pause or play the explanation video')
        ->assertSee('Preparing hazard clip')
        ->assertSee('Loading explanation')
        ->assertViewHas('practiceItems', function ($items) {
            return $items->pluck('item_type')->sort()->values()->all() === [
                ContentPage::TYPE_CGI_CLIPS,
                ContentPage::TYPE_MOTORWAY_SIGN,
                'question',
            ];
        });
});
