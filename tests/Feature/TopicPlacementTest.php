<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;

test('the public subsection only displays non-empty category and topic groups', function () {
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
        ->assertSee('Topics')
        ->assertSee('Visible direct topic')
        ->assertDontSee('Categories')
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
        ->assertSee('Categories')
        ->assertSee('Visible category')
        ->assertDontSee('Topics')
        ->assertDontSee('No categories available');

    $empty = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Empty Area',
    ]);

    $this->get(route('frontend.sub_section', $empty))
        ->assertOk()
        ->assertDontSee('Categories')
        ->assertDontSee('Topics')
        ->assertDontSee('No categories available');
});

test('admin parent pages display topics attached directly to them', function () {
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
        ->assertSee('Topics in Theory')
        ->assertSee('Section level topic');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.sections.sub_sections.show', [$section, $subSection]))
        ->assertOk()
        ->assertSee('Topics in Practice')
        ->assertSee('Subsection level topic');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.sections.sub_sections.categories.show', [$section, $subSection, $category]))
        ->assertOk()
        ->assertSee('Category level topic');
});

test('topic creation selects a parent by name and validates the selected parent', function () {
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
        ])
        ->assertRedirect(route('admin.topics.index'));

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
        ])
        ->assertSessionHasErrors('topicable_id');
});
