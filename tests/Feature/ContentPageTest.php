<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\ContentPage;
use App\Models\Question;
use App\Models\Section;
use App\Models\SubSection;
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

function learningPageCategory(): Category
{
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Road knowledge',
    ]);

    return Category::create([
        'sub_section_id' => $subSection->id,
        'name_en' => 'Motorways',
        'name_ku' => null,
    ]);
}

test('an admin can create a CGI page with hazard and explanation videos', function () {
    Storage::fake('public');
    $category = learningPageCategory();

    $response = $this->actingAs(learningPageAdmin(), 'admin')->post(route('admin.content-pages.store'), [
        'category_id' => $category->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
        'text_en' => 'Compare how the vehicles move through the bend.',
        'clips' => [
            0 => ['media' => UploadedFile::fake()->create('hazard.mp4', 100, 'video/mp4')],
            1 => ['media' => UploadedFile::fake()->create('explanation.mp4', 100, 'video/mp4')],
        ],
    ]);

    $response->assertRedirect(route('admin.content-pages.index'))->assertSessionHasNoErrors();
    $this->assertDatabaseHas('content_pages', [
        'category_id' => $category->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
        'text_en' => 'Compare how the vehicles move through the bend.',
    ]);
    $this->assertDatabaseHas('cgi_clips', [
        'slot' => 0,
        'media_url' => null,
    ]);
    $this->assertDatabaseHas('cgi_clips', ['slot' => 1, 'media_url' => null]);
    $this->get(route('admin.content-pages.index'))->assertOk()->assertSee('CGI clips');
});

test('a CGI page requires at least one remaining clip', function () {
    $category = learningPageCategory();
    $page = ContentPage::create([
        'category_id' => $category->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
    ]);
    $clip = $page->clips()->create([
        'slot' => 0,
        'media_url' => 'https://example.com/existing.mp4',
    ]);

    $response = $this->actingAs(learningPageAdmin(), 'admin')->put(route('admin.content-pages.update', $page), [
        'category_id' => $category->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
        'clips' => [
            0 => ['remove' => '1'],
        ],
    ]);

    $response->assertSessionHasErrors('clips');
    expect($clip->fresh())->not->toBeNull();
});

test('an admin can create a motorway sign page with an explanation', function () {
    Storage::fake('public');
    $category = learningPageCategory();

    $response = $this->actingAs(learningPageAdmin(), 'admin')->post(route('admin.content-pages.store'), [
        'category_id' => $category->id,
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
        'sign_image' => UploadedFile::fake()->image('motorway-sign.png', 600, 600),
        'explanation_en' => 'This sign marks the beginning of motorway regulations.',
    ]);

    $response->assertRedirect(route('admin.content-pages.index'))->assertSessionHasNoErrors();
    $page = ContentPage::where('type', ContentPage::TYPE_MOTORWAY_SIGN)->firstOrFail();

    expect($page->explanation_en)->toBe('This sign marks the beginning of motorway regulations.');
    Storage::disk('public')->assertExists(str_replace('/storage/', '', $page->getRawOriginal('sign_image_path')));
});

test('a motorway sign page requires both an image and an explanation', function () {
    $category = learningPageCategory();

    $response = $this->actingAs(learningPageAdmin(), 'admin')->post(route('admin.content-pages.store'), [
        'category_id' => $category->id,
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
    ]);

    $response->assertSessionHasErrors(['sign_image', 'explanation_en']);
});

test('practice contains questions CGI pages and motorway sign pages', function () {
    $category = learningPageCategory();
    $question = Question::create([
        'category_id' => $category->id,
        'text_en' => 'What should you do?',
    ]);
    $question->choices()->createMany([
        ['text_en' => 'Slow down', 'is_correct' => true],
        ['text_en' => 'Speed up', 'is_correct' => false],
        ['text_en' => 'Stop immediately', 'is_correct' => false],
        ['text_en' => 'Sound the horn', 'is_correct' => false],
    ]);

    $cgiPage = ContentPage::create([
        'category_id' => $category->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
    ]);
    $cgiPage->clips()->create([
        'slot' => 0,
        'media_url' => 'https://example.com/cgi.mp4',
    ]);
    ContentPage::create([
        'category_id' => $category->id,
        'type' => ContentPage::TYPE_MOTORWAY_SIGN,
        'sign_image_path' => '/storage/content-pages/signs/example.png',
        'explanation_en' => 'Motorway regulations begin here.',
    ]);

    $response = $this->get(route('theory.practice', $category));

    $response->assertOk()->assertViewHas('practiceItems', function ($items) {
        return $items->pluck('item_type')->sort()->values()->all() === [
            ContentPage::TYPE_CGI_CLIPS,
            ContentPage::TYPE_MOTORWAY_SIGN,
            'question',
        ];
    });
});
