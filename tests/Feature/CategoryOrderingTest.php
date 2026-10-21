<?php

use App\Models\Admin;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;

test('admins can drag categories into a persistent order used by learner pages', function () {
    $admin = Admin::create([
        'name' => 'Ordering Admin',
        'email' => 'ordering-admin@example.com',
        'password' => bcrypt('password'),
    ]);
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Practice']);
    $first = $subSection->topics()->create(['name_en' => 'First', 'content_type' => Topic::TYPE_QUESTIONS]);
    $second = $subSection->topics()->create(['name_en' => 'Second', 'content_type' => Topic::TYPE_QUESTIONS]);
    $third = $subSection->topics()->create(['name_en' => 'Third', 'content_type' => Topic::TYPE_QUESTIONS]);

    expect([$first->position, $second->position, $third->position])->toBe([1, 2, 3]);

    $this->actingAs($admin, 'admin')->patchJson(route('admin.topics.reorder'), [
        'category_ids' => [$third->id, $first->id, $second->id],
    ])->assertOk()->assertJsonPath('message', 'Category order saved.');

    expect($third->fresh()->position)->toBe(1)
        ->and($first->fresh()->position)->toBe(2)
        ->and($second->fresh()->position)->toBe(3);

    $this->get(route('frontend.sub_section', $subSection))
        ->assertOk()
        ->assertSeeInOrder(['Third', 'First', 'Second']);

    $this->actingAs($admin, 'admin')->get(route('admin.sections.sub_sections.show', [$section, $subSection]))
        ->assertOk()
        ->assertSee('data-category-sort', false)
        ->assertSeeInOrder(['Third', 'First', 'Second']);
});

test('a new category returns the admin to its placement and is appended there', function () {
    $admin = Admin::create([
        'name' => 'Placement Admin',
        'email' => 'placement-admin@example.com',
        'password' => bcrypt('password'),
    ]);
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Revision']);
    $subSection->topics()->create(['name_en' => 'Existing', 'content_type' => Topic::TYPE_QUESTIONS]);

    $this->actingAs($admin, 'admin')->post(route('admin.topics.store'), [
        'topicable_type' => SubSection::class,
        'topicable_id' => $subSection->id,
        'name_en' => 'New category',
        'content_type' => Topic::TYPE_QUESTIONS,
        'color' => '#1664f5',
    ])->assertRedirect(route('admin.sections.sub_sections.show', [$section, $subSection]));

    expect(Topic::where('name_en', 'New category')->firstOrFail()->position)->toBe(2);
});
