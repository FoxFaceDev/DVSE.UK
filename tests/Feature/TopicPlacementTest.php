<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;

test('the public subsection only displays non-empty category groups and direct categories', function () {
    $section = Section::create(['name' => 'Theory']);
    $topicsOnly = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Learning Area',
    ]);
    Topic::create([
        'topicable_type' => SubSection::class,
        'topicable_id' => $topicsOnly->id,
        'name_en' => 'Visible direct topic',
    ]);

    $this->get(route('frontend.sub_section', $topicsOnly))
        ->assertOk()
        ->assertSee('Categories')
        ->assertSee('Visible direct topic')
        ->assertDontSee('Category groups')
        ->assertDontSee('No categories available');

    $categoriesOnly = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Category Area',
    ]);
    Category::create([
        'sub_section_id' => $categoriesOnly->id,
        'name_en' => 'Visible category',
    ]);

    $this->get(route('frontend.sub_section', $categoriesOnly))
        ->assertOk()
        ->assertSee('Category groups')
        ->assertSee('Visible category')
        ->assertDontSee('<h2 class="mb-4 font-heading text-xl font-bold text-gray-700">Categories</h2>', false)
        ->assertDontSee('No categories available');

    $empty = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Empty Area',
    ]);

    $this->get(route('frontend.sub_section', $empty))
        ->assertOk()
        ->assertDontSee('Category groups')
        ->assertDontSee('<h2 class="mb-4 font-heading text-xl font-bold text-gray-700">Categories</h2>', false)
        ->assertDontSee('No categories available');
});

test('admin parent pages display categories attached directly to them', function () {
    $admin = Admin::create([
        'name' => 'Topic Admin',
        'email' => 'topic-admin@example.com',
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
    Topic::create([
        'topicable_type' => Section::class,
        'topicable_id' => $section->id,
        'name_en' => 'Section level topic',
    ]);
    Topic::create([
        'topicable_type' => SubSection::class,
        'topicable_id' => $subSection->id,
        'name_en' => 'Subsection level topic',
    ]);
    Topic::create([
        'topicable_type' => Category::class,
        'topicable_id' => $category->id,
        'name_en' => 'Category level topic',
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.sections.show', $section))
        ->assertOk()
        ->assertSee('Categories in Theory')
        ->assertSee('Section level topic');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.sections.sub_sections.show', [$section, $subSection]))
        ->assertOk()
        ->assertSee('Categories in Practice')
        ->assertSee('Subsection level topic');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.sections.sub_sections.categories.show', [$section, $subSection, $category]))
        ->assertOk()
        ->assertSee('Category level topic');
});

test('category creation selects a parent by name and validates the selected parent', function () {
    $admin = Admin::create([
        'name' => 'Topic Admin',
        'email' => 'topic-create@example.com',
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

    $this->actingAs($admin, 'admin')
        ->get(route('admin.topics.create', [
            'topicable_type' => Category::class,
            'topicable_id' => $category->id,
        ]))
        ->assertOk()
        ->assertSee('Parent Name')
        ->assertDontSee('Parent ID')
        ->assertSee('Road rules');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.topics.store'), [
            'topicable_type' => Category::class,
            'topicable_id' => $category->id,
            'name_en' => 'New named-parent topic',
            'content_type' => Topic::TYPE_QUESTIONS,
            'color' => '#0452bc',
        ])
        ->assertRedirect(route('admin.sections.sub_sections.categories.show', [$section, $subSection, $category]));

    $this->assertDatabaseHas('topics', [
        'topicable_type' => Category::class,
        'topicable_id' => $category->id,
        'name_en' => 'New named-parent topic',
    ]);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.topics.store'), [
            'topicable_type' => Category::class,
            'topicable_id' => 999999,
            'name_en' => 'Invalid parent topic',
            'content_type' => Topic::TYPE_QUESTIONS,
            'color' => '#0452bc',
        ])
        ->assertSessionHasErrors('topicable_id');
});
