<?php

use App\Models\Admin;
use App\Models\Section;
use App\Models\Topic;
use App\Support\HazardPage;

test('admins create and edit cards using RGB and HSL codes', function () {
    $admin = Admin::create(['name' => 'Color editor', 'email' => 'colors@example.com', 'password' => bcrypt('password')]);
    $section = Section::create(['name' => 'Theory Test']);
    $this->actingAs($admin, 'admin');
    $this->get(route('admin.topics.create'))->assertOk()->assertSee('HEX, RGB, or HSL');
    $data = ['topicable_type' => Section::class, 'topicable_id' => $section->id, 'name_en' => 'Color card', 'content_type' => Topic::TYPE_QUESTIONS];
    $this->post(route('admin.topics.store'), $data + ['color' => 'rgb(247, 181, 0)'])->assertSessionHasNoErrors()->assertRedirect();
    $topic = $section->topics()->firstOrFail();
    expect($topic->color)->toBe('#f7b500');
    $this->get(route('admin.topics.edit', $topic))->assertOk()->assertSee('HEX, RGB, or HSL');
    $this->put(route('admin.topics.update', $topic), $data + ['color' => 'hsl(120, 100%, 50%)'])->assertSessionHasNoErrors()->assertRedirect();
    expect($topic->fresh()->color)->toBe('#00ff00');
    $this->put(route('admin.topics.update', $topic), $data + ['color' => 'rgb(999, 0, 0)'])->assertSessionHasErrors('color');
    expect($topic->fresh()->color)->toBe('#00ff00');
});

test('section subsection and hazard colors accept the same codes', function () {
    $admin = Admin::create(['name' => 'Theme editor', 'email' => 'theme-colors@example.com', 'password' => bcrypt('password')]);
    $section = Section::create(['name' => 'Theory Test']);
    $subSection = $section->subSections()->create(['name' => 'Theory Test']);
    $this->actingAs($admin, 'admin');
    $this->put(route('admin.sections.update', $section), ['name' => $section->name, 'color' => 'rgb(247 181 0)'])->assertSessionHasNoErrors();
    expect($section->fresh()->color)->toBe('#f7b500');
    $this->put(route('admin.sections.sub_sections.update', [$section, $subSection]), ['name' => $subSection->name, 'color' => '#abc'])->assertSessionHasNoErrors();
    expect($subSection->fresh()->color)->toBe('#aabbcc');
    $this->get(route('admin.sections.show', $section))->assertOk()->assertSee('HEX, RGB, or HSL');
    $settings = HazardPage::defaults();
    $settings['cards']['guarantee']['color'] = 'hsl(120 100% 50%)';
    $this->put(route('admin.hazard-page.update'), $settings)->assertSessionHasNoErrors();
    expect(HazardPage::settings()['cards']['guarantee']['color'])->toBe('#00ff00');
});
