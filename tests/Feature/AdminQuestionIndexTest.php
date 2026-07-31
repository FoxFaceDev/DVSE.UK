<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Question;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;

test('the admin question index displays and filters questions by topic', function () {
    $admin = Admin::create([
        'name' => 'Question Admin',
        'email' => 'questions@example.com',
        'password' => bcrypt('password'),
    ]);
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Practice',
    ]);
    $category = Category::create([
        'sub_section_id' => $subSection->id,
        'name_en' => 'Road rules',
    ]);
    $selectedTopic = Topic::create([
        'topicable_type' => Category::class,
        'topicable_id' => $category->id,
        'name_en' => 'Selected topic',
    ]);
    $otherTopic = Topic::create([
        'topicable_type' => Category::class,
        'topicable_id' => $category->id,
        'name_en' => 'Other topic',
    ]);
    Question::create([
        'topic_id' => $selectedTopic->id,
        'text_en' => 'Question shown by the filter',
    ]);
    Question::create([
        'topic_id' => $otherTopic->id,
        'text_en' => 'Question hidden by the filter',
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.questions.index'))
        ->assertOk()
        ->assertSee('Selected topic')
        ->assertSee('Question shown by the filter')
        ->assertSee('Question hidden by the filter');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.questions.index', ['topic_id' => $selectedTopic->id]))
        ->assertOk()
        ->assertSee('Question shown by the filter')
        ->assertDontSee('Question hidden by the filter');
});
