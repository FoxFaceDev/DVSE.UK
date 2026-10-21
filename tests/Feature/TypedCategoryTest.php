<?php

use App\Models\Admin;
use App\Models\Question;
use App\Models\Section;
use App\Models\Topic;

test('admins create a category with a placement content type color and icon-ready card', function () {
    $admin = Admin::create([
        'name' => 'Category Admin',
        'email' => 'category-admin@example.com',
        'password' => bcrypt('password'),
    ]);
    $section = Section::create(['name' => 'Theory Test']);

    $this->actingAs($admin, 'admin')->post(route('admin.topics.store'), [
        'topicable_type' => Section::class,
        'topicable_id' => $section->id,
        'name_en' => 'Safe driving',
        'content_type' => Topic::TYPE_QUESTIONS,
        'color' => '#e11d48',
    ])->assertRedirect(route('admin.sections.show', $section))->assertSessionHasNoErrors();

    $category = Topic::firstOrFail();
    expect($category->content_type)->toBe(Topic::TYPE_QUESTIONS)
        ->and($category->color)->toBe('#e11d48');

    Question::create([
        'topic_id' => $category->id,
        'question_type' => 'text',
        'text_en' => 'How should you approach a bend?',
    ]);

    $this->get(route('v2.revision'))
        ->assertOk()
        ->assertSee('Safe driving')
        ->assertSee('#e11d48', false)
        ->assertSee('name="categories[]"', false);
});

test('category content types prevent questions and learning pages from being mixed', function () {
    $admin = Admin::create([
        'name' => 'Category Admin',
        'email' => 'typed-category-admin@example.com',
        'password' => bcrypt('password'),
    ]);
    $section = Section::create(['name' => 'Theory Test']);
    $learningCategory = Topic::create([
        'topicable_type' => Section::class,
        'topicable_id' => $section->id,
        'name_en' => 'Hazard learning',
        'content_type' => Topic::TYPE_LEARNING_PAGES,
        'color' => '#0f766e',
    ]);

    $choices = collect(range(1, 4))->map(fn ($number) => [
        'translations' => ['en' => ['text' => "Answer {$number}"]],
    ])->all();

    $this->actingAs($admin, 'admin')->post(route('admin.questions.store'), [
        'category_id' => $learningCategory->id,
        'question_type' => 'text',
        'translations' => ['en' => ['text' => 'This must not be stored']],
        'choices' => $choices,
        'correct_choice' => 0,
    ])->assertSessionHasErrors('category_id');

    expect(Question::count())->toBe(0);
});

test('revision displays empty question categories immediately in their admin order', function () {
    $section = Section::create(['name' => 'Theory Test']);
    $revision = $section->subSections()->create(['name' => 'Practice Revision Question']);
    $withQuestions = $revision->topics()->create([
        'name_en' => 'Rule of the Road',
        'content_type' => Topic::TYPE_QUESTIONS,
        'position' => 2,
    ]);
    $empty = $revision->topics()->create([
        'name_en' => 'Video Clips',
        'content_type' => Topic::TYPE_QUESTIONS,
        'position' => 1,
    ]);
    Question::create([
        'topic_id' => $withQuestions->id,
        'question_type' => 'text',
        'text_en' => 'A revision question',
    ]);

    $this->get(route('v2.revision'))
        ->assertOk()
        ->assertSeeInOrder(['Video Clips', 'Rule of the Road'])
        ->assertSee('data-topic="'.$empty->id.'" data-total="0"', false);
});
