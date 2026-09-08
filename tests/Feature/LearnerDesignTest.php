<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('learn navigation opens a dedicated page with current admin managed sections', function () {
    $section = Section::create(['name' => 'Custom learning', 'color' => '#b45309']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Custom practice']);

    $this->get(route('home'))->assertOk()->assertSee('href="'.route('learn').'"', false);
    $this->get(route('learn'))->assertOk()->assertSee('Custom learning')->assertSee('Custom practice')
        ->assertSee(route('frontend.sub_section', $subSection), false)->assertSee('#b45309');

    $admin = Admin::create(['name' => 'Design Admin', 'email' => 'design@example.com', 'password' => bcrypt('password')]);
    $this->actingAs($admin, 'admin')->put(route('admin.sections.update', $section), [
        'name' => 'Renamed learning', 'color' => '#7c3aed',
    ])->assertRedirect();

    $this->get(route('learn'))->assertOk()->assertSee('Renamed learning')->assertSee('#7c3aed')->assertDontSee('Custom learning');
    $this->get(route('home'))->assertOk()->assertSee('Renamed learning')->assertSee('#7c3aed');
});

test('category and topic cards retain translations counts routes and inherited colours', function () {
    $section = Section::create(['name' => 'Learning', 'color' => '#b45309']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Practice', 'color' => '#047857']);
    $category = Category::create(['sub_section_id' => $subSection->id, 'name_en' => 'Road awareness', 'name_ku' => 'ئاگاداری']);
    $topic = $category->topics()->create(['name_en' => 'Junctions', 'name_ku' => 'چوارڕێیان']);

    $this->get(route('frontend.sub_section', $subSection))->assertOk()->assertSee('Road awareness')->assertSee('ئاگاداری')
        ->assertSee('1 topic')->assertSee('#047857')->assertSee(route('frontend.category', $category), false);
    $this->get(route('frontend.category', $category))->assertOk()->assertSee('Junctions')->assertSee('چوارڕێیان')
        ->assertSee('0 questions')->assertSee('#047857')->assertSee(route('theory.practice', $topic), false);

    $subSection->update(['color' => null]);
    $this->get(route('frontend.category', $category))->assertOk()->assertSee('--learning-color: #b45309', false);
});

test('admin icon replacements are immediately served to admin and learner cards', function () {
    Storage::fake('public');
    $admin = Admin::create(['name' => 'Icon Admin', 'email' => 'icons@example.com', 'password' => bcrypt('password')]);
    $section = Section::create(['name' => 'Theory', 'color' => '#245aa2']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Practice']);

    $this->actingAs($admin, 'admin')->put(route('admin.sections.sub_sections.update', [$section, $subSection]), [
        'name' => 'Practice',
        'color' => '#245aa2',
        'icon' => UploadedFile::fake()->image('first.png'),
    ])->assertRedirect();

    $subSection->refresh();
    $firstPath = str_replace('/storage/', '', $subSection->getRawOriginal('icon_path'));
    Storage::disk('public')->assertExists($firstPath);
    $this->get($subSection->icon_path)->assertOk()->assertHeader('content-type', 'image/png');
    $this->get(route('admin.sections.show', $section))->assertSee($subSection->icon_path, false);
    $this->get(route('frontend.section', $section))->assertSee($subSection->icon_path, false);

    $this->actingAs($admin, 'admin')->put(route('admin.sections.sub_sections.update', [$section, $subSection]), [
        'name' => 'Practice',
        'color' => '#245aa2',
        'icon' => UploadedFile::fake()->image('replacement.png'),
    ])->assertRedirect();

    $subSection->refresh();
    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('public')->assertExists(str_replace('/storage/', '', $subSection->getRawOriginal('icon_path')));
});

test('mock tests appear after categories and topics', function () {
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Practice']);
    Category::create(['sub_section_id' => $subSection->id, 'name_en' => 'Rules']);
    $subSection->topics()->create(['name_en' => 'Signs']);
    $subSection->mockTests()->create([
        'name' => 'Final Mock', 'type' => 'theory', 'question_count' => 1,
        'pass_mark' => 1, 'duration_minutes' => 1, 'is_active' => true,
    ]);

    $this->get(route('frontend.sub_section', $subSection))
        ->assertOk()
        ->assertSeeInOrder(['Categories', 'Topics', 'Mock tests', 'Final Mock']);
});

test('admins can edit section and subsection card descriptions', function () {
    $admin = Admin::create(['name' => 'Content Admin', 'email' => 'content@example.com', 'password' => bcrypt('password')]);
    $section = Section::create(['name' => 'Theory', 'color' => '#245aa2']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Practice']);

    $this->actingAs($admin, 'admin')->get(route('admin.home'))
        ->assertOk()
        ->assertSee('Card Description')
        ->assertSee('name="description"', false);

    $this->actingAs($admin, 'admin')->put(route('admin.sections.update', $section), [
        'name' => 'Theory',
        'description' => 'Learn the rules before you take the road.',
        'color' => '#245aa2',
    ])->assertRedirect();

    $this->actingAs($admin, 'admin')->put(route('admin.sections.sub_sections.update', [$section, $subSection]), [
        'name' => 'Practice',
        'description' => 'Build confidence with focused practice.',
        'color' => '#245aa2',
    ])->assertRedirect();

    expect($section->refresh()->description)->toBe('Learn the rules before you take the road.');
    expect($subSection->refresh()->description)->toBe('Build confidence with focused practice.');

    $this->get(route('home'))->assertOk()->assertSee('Learn the rules before you take the road.');
    $this->get(route('learn'))->assertOk()->assertSee('Build confidence with focused practice.');
    $this->get(route('frontend.section', $section))->assertOk()->assertSee('Build confidence with focused practice.');
});

test('cards use the default description when an admin leaves it empty', function () {
    $section = Section::create(['name' => 'Theory']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Choose your next step in your learning journey.');
});
