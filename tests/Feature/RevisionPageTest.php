<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Section;
use App\Models\Topic;
use App\Support\RevisionCatalog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('revision groups open the supplied design with every editable reference card', function () {
    $admin = Admin::create(['name' => 'Revision editor', 'email' => 'revision-editor@example.com', 'password' => bcrypt('password')]);
    $section = Section::where('home_key', 'theory')->firstOrFail();
    $subSection = $section->subSections()->create(['name' => 'Theory Test']);

    $this->actingAs($admin, 'admin')->post(route('admin.sections.sub_sections.categories.store', [$section, $subSection]), [
        'name_en' => 'Practice Revision Question',
        'page_type' => 'revision',
    ])->assertRedirect()->assertSessionHasNoErrors();
    $group = Category::where('page_type', 'revision')->firstOrFail();
    expect($group->topics()->count())->toBe(15);
    $response = $this->get(route('frontend.category', $group))->assertOk()->assertViewIs('v2.revision')
        ->assertSee('design-v2/Practice/css/practice.css', false)->assertSee('All categories');
    foreach (RevisionCatalog::CARDS as $icon => $name) {
        $response->assertSee($name)->assertSee('design-v2/Practice/img/practice/'.$icon.'.svg', false);
    }
    $this->get(route('admin.topics.index', ['revision' => 1]))->assertOk()->assertSee('Add Revision Card')->assertSee('Video Clips');

    $this->actingAs($admin, 'admin')->post(route('admin.topics.store'), [
        'topicable_type' => Category::class, 'topicable_id' => $group->id,
        'name_en' => 'City junctions', 'content_type' => Topic::TYPE_QUESTIONS,
        'color' => '#0d9488', 'icon_preset' => 'rules-of-the-road',
    ])->assertRedirect()->assertSessionHasNoErrors();
    $newCard = $group->topics()->where('name_en', 'City junctions')->firstOrFail();
    $this->get(route('frontend.category', $group))->assertOk()->assertSee('City junctions')->assertSee('--revision-color: #0d9488', false);

    $newCard->questions()->create(['text_en' => 'Who has priority at this junction?']);
    $this->get(route('frontend.category', $group))->assertOk()->assertSee('data-topic="'.$newCard->id.'" data-total="1"', false);
    $this->actingAs($admin, 'admin')->put(route('admin.topics.update', $newCard), [
        'topicable_type' => Category::class, 'topicable_id' => $group->id,
        'name_en' => 'Updated junctions', 'content_type' => Topic::TYPE_QUESTIONS,
        'color' => '#0d9488', 'icon_preset' => 'alertness',
    ])->assertSessionHasNoErrors();
    $this->get(route('frontend.category', $group))->assertOk()->assertSee('Updated junctions')->assertDontSee('City junctions');

    $deleted = $group->topics()->where('name_en', 'Attitude')->firstOrFail();
    $this->actingAs($admin, 'admin')->delete(route('admin.topics.destroy', $deleted))->assertRedirect();
    $this->actingAs($admin, 'admin')->put(route('admin.sections.sub_sections.categories.update', [$section, $subSection, $group]), ['name_en' => 'My revision area'])
        ->assertRedirect()->assertSessionHasNoErrors();
    $this->get(route('frontend.category', $group))->assertOk()->assertViewIs('v2.revision')->assertDontSee('Attitude');
});

test('revision counts and continue stay inside the chosen group and selected cards', function () {
    $section = Section::where('home_key', 'theory')->firstOrFail();
    $subSection = $section->subSections()->create(['name' => 'Theory Test']);
    $group = $subSection->categories()->create(['name_en' => 'Practice Revision Question', 'page_type' => 'revision']);
    $first = $group->topics()->create(['name_en' => 'First card']);
    $second = $group->topics()->create(['name_en' => 'Second card']);
    $other = $subSection->topics()->create(['name_en' => 'Unrelated questions']);
    $firstQuestion = $first->questions()->create(['text_en' => 'First question']);
    $secondQuestion = $second->questions()->create(['text_en' => 'Second question']);
    $other->questions()->create(['text_en' => 'Unrelated question']);

    $this->get(route('frontend.category', $group))->assertOk()->assertDontSee('Unrelated questions')
        ->assertSee('name="group" value="'.$group->id.'"', false)->assertSee('data-topic="all" data-total="2"', false);
    $this->get(route('v2.revise', ['group' => $group->id, 'categories' => [$second->id]]))->assertOk()
        ->assertViewHas('questions', fn ($questions) => $questions->pluck('id')->all() === [$secondQuestion->id]);
    $this->get(route('v2.revise', ['group' => $group->id]))->assertOk()
        ->assertViewHas('questions', fn ($questions) => $questions->pluck('id')->sort()->values()->all() === [$firstQuestion->id, $secondQuestion->id]);
    $this->get(route('v2.revise', ['group' => $group->id, 'categories' => [$other->id]]))->assertOk()
        ->assertViewHas('questions', fn ($questions) => $questions->isEmpty());
});

test('uploaded revision icons can be replaced with bundled SVGs without deleting those assets', function () {
    Storage::fake('public');
    $admin = Admin::create(['name' => 'Icon editor', 'email' => 'revision-icons@example.com', 'password' => bcrypt('password')]);
    $section = Section::where('home_key', 'theory')->firstOrFail();
    $card = $section->topics()->create(['name_en' => 'Custom card']);
    $data = ['topicable_type' => Section::class, 'topicable_id' => $section->id, 'name_en' => $card->name_en, 'content_type' => Topic::TYPE_QUESTIONS, 'color' => '#1664f5', 'icon_preset' => 'alertness'];

    $this->actingAs($admin, 'admin')->put(route('admin.topics.update', $card), $data + ['icon' => UploadedFile::fake()->image('custom.png')])->assertSessionHasNoErrors();
    $uploadedPath = $card->fresh()->getRawOriginal('icon_path');
    Storage::disk('public')->assertExists($uploadedPath);
    $this->actingAs($admin, 'admin')->put(route('admin.topics.update', $card), $data + ['remove_icon' => 1])->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($uploadedPath);
    expect($card->fresh()->icon_path)->toBe(asset('design-v2/Practice/img/practice/alertness.svg'));
    expect(is_file(public_path('design-v2/Practice/img/practice/alertness.svg')))->toBeTrue();
});
