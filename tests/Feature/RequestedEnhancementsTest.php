<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Choice;
use App\Models\Language;
use App\Models\Question;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function enhancementAdmin(bool $superadmin = false): Admin
{
    return Admin::create(['name' => 'Admin', 'email' => uniqid().'@example.com', 'password' => 'password', 'is_superadmin' => $superadmin]);
}

function enhancementTopic(): Topic
{
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Practice']);
    $category = Category::create(['sub_section_id' => $subSection->id, 'name_en' => 'Rules']);
    return Topic::create(['topicable_type' => Category::class, 'topicable_id' => $category->id, 'name_en' => 'Signs']);
}

test('admins can add a language and save tabbed question translations once', function () {
    $admin = enhancementAdmin();
    $topic = enhancementTopic();
    $this->actingAs($admin, 'admin')->post(route('admin.languages.store'), ['name' => 'Urdu', 'code' => 'ur', 'direction' => 'rtl', 'is_active' => '1'])->assertRedirect();

    $payload = [
        'topic_id' => $topic->id, 'question_type' => 'text', 'correct_choice' => 2,
        'translations' => ['en' => ['text' => 'Choose one'], 'ur' => ['text' => 'Urdu question']],
        'choices' => collect(range(0, 3))->map(fn ($i) => ['translations' => ['en' => ['text' => "Answer $i"], 'ur' => ['text' => "Urdu $i"]]])->all(),
    ];
    $this->actingAs($admin, 'admin')->post(route('admin.questions.store'), $payload)->assertRedirect(route('admin.questions.index'));

    $question = Question::with('choices')->firstOrFail();
    expect(Language::where('code', 'ur')->exists())->toBeTrue()
        ->and($question->translations['ur']['text'])->toBe('Urdu question')
        ->and($question->choices->where('is_correct', true)->first()->text_en)->toBe('Answer 2');
});

test('image answer questions store four shared answer images', function () {
    Storage::fake('public');
    $admin = enhancementAdmin();
    $topic = enhancementTopic();
    $choices = [];
    foreach (range(0, 3) as $index) $choices[$index] = ['image' => UploadedFile::fake()->image("$index.png")];

    $this->actingAs($admin, 'admin')->post(route('admin.questions.store'), [
        'topic_id' => $topic->id, 'question_type' => 'image_answers', 'correct_choice' => 1,
        'translations' => ['en' => ['text' => 'Which sign?']], 'choices' => $choices,
    ])->assertRedirect(route('admin.questions.index'));

    expect(Question::first()->question_type)->toBe('image_answers')->and(Choice::whereNotNull('image_path')->count())->toBe(4);
});

test('only superadmins can manage users and reset their passwords', function () {
    $user = User::factory()->create(['password' => 'old-password']);
    $this->actingAs(enhancementAdmin(), 'admin')->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs(enhancementAdmin(true), 'admin')->put(route('admin.users.reset-password', $user), ['password' => 'new-password', 'password_confirmation' => 'new-password'])->assertRedirect();
    expect(password_verify('new-password', $user->fresh()->password))->toBeTrue();
});

test('mock test results retain incorrect answers for review', function () {
    $topic = enhancementTopic();
    $question = Question::create(['topic_id' => $topic->id, 'text_en' => 'What is correct?']);
    $wrong = $question->choices()->create(['text_en' => 'Wrong', 'is_correct' => false]);
    $question->choices()->create(['text_en' => 'Right', 'is_correct' => true]);

    $this->post(route('theory.mock_test_submit'), ['question_ids' => [$question->id], 'answers' => [$question->id => $wrong->id]])->assertRedirect();
    $this->get(route('theory.mock_test_result'))->assertOk()->assertSee('Review your mistakes')->assertSee('What is correct?')->assertSee('Right');
});

test('motorway additional signs copy is editable per language and explanations replace english', function () {
    Storage::fake('public');
    $admin = enhancementAdmin();
    $topic = enhancementTopic();

    $this->actingAs($admin, 'admin')->post(route('admin.content-pages.store'), [
        'admin_title' => 'Translated motorway guide',
        'topic_id' => $topic->id,
        'type' => 'motorway_sign',
        'sign_image' => UploadedFile::fake()->image('sign.png'),
        'translations' => [
            'en' => [
                'explanation' => 'English explanation',
                'what_to_do' => 'English action',
                'additional_signs_title' => 'Related signs',
                'additional_signs_description' => 'Look for these signs.',
            ],
            'ku' => [
                'explanation' => 'Kurdish explanation',
                'what_to_do' => 'Kurdish action',
                'additional_signs_title' => 'Kurdish signs title',
                'additional_signs_description' => 'Kurdish signs description',
            ],
        ],
    ])->assertRedirect(route('admin.content-pages.index'));

    $page = \App\Models\ContentPage::firstOrFail();
    expect($page->translations['ku']['additional_signs_title'])->toBe('Kurdish signs title');

    $this->get(route('theory.practice', $topic))
        ->assertOk()
        ->assertSee('Kurdish signs title')
        ->assertSee('x-show="!showTranslation"', false)
        ->assertSee("translated(currentItem, 'additional_signs_description')", false);
});
