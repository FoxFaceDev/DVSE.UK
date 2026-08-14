<?php

use App\Models\Ad;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;
use Illuminate\Support\Facades\Mail;

function advertisementAdmin(): Admin
{
    return Admin::create([
        'name' => 'Advertisement Admin',
        'email' => 'advertisement-admin@example.com',
        'password' => bcrypt('password'),
    ]);
}

function advertisementCategories(): array
{
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Road topics',
    ]);

    return [
        Category::create(['sub_section_id' => $subSection->id, 'name_en' => 'Motorways']),
        Category::create(['sub_section_id' => $subSection->id, 'name_en' => 'Road signs']),
        Category::create(['sub_section_id' => $subSection->id, 'name_en' => 'Vehicle safety']),
    ];
}

function advertisementLanguage(string $code = 'en'): Language
{
    return Language::where('code', $code)->firstOrFail();
}

function advertisementLifecycleFields(): array
{
    return [
        'display_type' => 'question',
        'advertiser_email' => 'owner@example.com',
        'starts_at' => now()->subMinute()->format('Y-m-d H:i:s'),
        'expires_at' => now()->addMonth()->format('Y-m-d H:i:s'),
    ];
}

test('an advertisement can target multiple selected categories', function () {
    [$motorways, $roadSigns, $vehicleSafety] = advertisementCategories();

    $response = $this->actingAs(advertisementAdmin(), 'admin')->post(route('admin.ads.store'), [
        ...advertisementLifecycleFields(),
        'title' => 'Selected categories ad',
        'language_id' => advertisementLanguage()->id,
        'media_type' => 'image',
        'link_url' => 'https://example.com/offer',
        'target_all_categories' => '0',
        'category_ids' => [$motorways->id, $roadSigns->id],
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.ads.index'))->assertSessionHasNoErrors();
    $ad = Ad::where('title', 'Selected categories ad')->firstOrFail();

    expect($ad->targets_all_categories)->toBeFalse()
        ->and($ad->categories()->pluck('categories.id')->sort()->values()->all())->toBe([$motorways->id, $roadSigns->id]);
    $this->assertDatabaseMissing('ad_category', [
        'ad_id' => $ad->id,
        'category_id' => $vehicleSafety->id,
    ]);
});

test('select all stores an advertisement as a global category target', function () {
    $categories = advertisementCategories();

    $this->actingAs(advertisementAdmin(), 'admin')->post(route('admin.ads.store'), [
        ...advertisementLifecycleFields(),
        'title' => 'All categories ad',
        'language_id' => advertisementLanguage()->id,
        'media_type' => 'image',
        'link_url' => 'https://example.com/global-offer',
        'target_all_categories' => '1',
        'category_ids' => collect($categories)->pluck('id')->all(),
        'is_active' => '1',
    ])->assertRedirect(route('admin.ads.index'));

    $ad = Ad::where('title', 'All categories ad')->firstOrFail();
    expect($ad->targets_all_categories)->toBeTrue()
        ->and($ad->categories()->count())->toBe(0);
});

test('an advertisement can update its selected categories', function () {
    [$motorways, $roadSigns, $vehicleSafety] = advertisementCategories();
    $ad = Ad::create([
        'language_id' => advertisementLanguage()->id,
        'title' => 'Editable targeting ad',
        'media_type' => 'image',
        'link_url' => 'https://example.com/editable-offer',
        'targets_all_categories' => false,
        'is_active' => true,
    ]);
    $ad->categories()->attach($motorways);
    $admin = advertisementAdmin();

    $this->actingAs($admin, 'admin')->get(route('admin.ads.edit', $ad))
        ->assertOk()
        ->assertSee('Select all categories');

    $this->put(route('admin.ads.update', $ad), [
        ...advertisementLifecycleFields(),
        'title' => 'Editable targeting ad',
        'language_id' => advertisementLanguage()->id,
        'media_type' => 'image',
        'link_url' => 'https://example.com/editable-offer',
        'target_all_categories' => '0',
        'category_ids' => [$roadSigns->id, $vehicleSafety->id],
        'is_active' => '1',
    ])->assertRedirect(route('admin.ads.index'));

    expect($ad->fresh()->categories()->pluck('categories.id')->sort()->values()->all())
        ->toBe([$roadSigns->id, $vehicleSafety->id]);
});

test('at least one category is required when select all is off', function () {
    advertisementCategories();

    $this->actingAs(advertisementAdmin(), 'admin')->post(route('admin.ads.store'), [
        ...advertisementLifecycleFields(),
        'title' => 'Invalid targeting ad',
        'language_id' => advertisementLanguage()->id,
        'media_type' => 'image',
        'link_url' => 'https://example.com/invalid-offer',
        'target_all_categories' => '0',
        'is_active' => '1',
    ])->assertSessionHasErrors('category_ids');

    $this->assertDatabaseMissing('ads', ['title' => 'Invalid targeting ad']);
});

test('practice only receives ads that target its category or all categories', function () {
    [$motorways, $roadSigns] = advertisementCategories();
    $targetedAd = Ad::create([
        'language_id' => advertisementLanguage()->id,
        'title' => 'Road signs only',
        'media_type' => 'image',
        'link_url' => 'https://example.com/road-signs',
        'targets_all_categories' => false,
        'is_active' => true,
    ]);
    $targetedAd->categories()->attach($roadSigns);

    $topicMotorways = Topic::create(['topicable_type' => 'App\Models\Category', 'topicable_id' => $motorways->id, 'name_en' => 'Motorways Topic']);
    $topicRoadSigns = Topic::create(['topicable_type' => 'App\Models\Category', 'topicable_id' => $roadSigns->id, 'name_en' => 'Road Signs Topic']);

    $this->get(route('theory.practice', $topicMotorways))
        ->assertOk()
        ->assertViewHas('ads', fn ($ads) => $ads->isEmpty());

    $this->get(route('theory.practice', $topicRoadSigns))
        ->assertOk()
        ->assertViewHas('ads', fn ($ads) => $ads->contains(fn ($ad) => $ad->is($targetedAd)));

    $globalAd = Ad::create([
        'language_id' => advertisementLanguage()->id,
        'title' => 'Every category',
        'media_type' => 'image',
        'link_url' => 'https://example.com/every-category',
        'targets_all_categories' => true,
        'is_active' => true,
    ]);

    $this->get(route('theory.practice', $topicMotorways))
        ->assertOk()
        ->assertViewHas('ads', fn ($ads) => $ads->contains(fn ($ad) => $ad->is($globalAd)));
});

test('practice receives ads matching the learners account language', function () {
    [$category] = advertisementCategories();
    $english = advertisementLanguage('en');
    $kurdish = advertisementLanguage('ku');

    foreach ([$english, $kurdish] as $language) {
        Ad::create([
            'language_id' => $language->id,
            'title' => $language->name.' advertisement',
            'media_type' => 'image',
            'link_url' => 'https://example.com/'.$language->code,
            'targets_all_categories' => true,
            'is_active' => true,
        ]);
    }

    $topic = Topic::create([
        'topicable_type' => 'App\\Models\\Category',
        'topicable_id' => $category->id,
        'name_en' => 'Language ad topic',
    ]);

    $this->get(route('theory.practice', $topic))
        ->assertOk()
        ->assertViewHas('ads', fn ($ads) => $ads->pluck('language_id')->values()->all() === [$english->id])
        ->assertSee('ad.language_id === null || String(ad.language_id) === String(languageId)', false);
});

test('admins can search advertisements by title link language or category', function () {
    [$motorways, $roadSigns] = advertisementCategories();
    $english = advertisementLanguage('en');
    $kurdish = advertisementLanguage('ku');

    $motorwayAd = Ad::create([
        'language_id' => $english->id,
        'title' => 'Winter driving offer',
        'media_type' => 'image',
        'link_url' => 'https://example.com/winter-campaign',
        'targets_all_categories' => false,
        'is_active' => true,
    ]);
    $motorwayAd->categories()->attach($motorways);

    $roadSignsAd = Ad::create([
        'language_id' => $kurdish->id,
        'title' => 'Summer lessons',
        'media_type' => 'image',
        'link_url' => 'https://example.com/summer-campaign',
        'targets_all_categories' => false,
        'is_active' => true,
    ]);
    $roadSignsAd->categories()->attach($roadSigns);

    $admin = advertisementAdmin();

    foreach (['Winter driving', 'winter-campaign', 'Motorways'] as $search) {
        $this->actingAs($admin, 'admin')->get(route('admin.ads.index', ['q' => $search]))
            ->assertOk()
            ->assertSee('Winter driving offer')
            ->assertDontSee('Summer lessons');
    }

    $this->actingAs($admin, 'admin')->get(route('admin.ads.index', ['q' => 'Kurdish']))
        ->assertOk()
        ->assertSee('Summer lessons')
        ->assertDontSee('Winter driving offer')
        ->assertSee('name="q"', false);
});

test('an SMTP failure does not undo a successfully created advertisement', function () {
    Mail::shouldReceive('to')->once()->with('owner@example.com')->andReturnSelf();
    Mail::shouldReceive('send')->once()->andThrow(new RuntimeException('SMTP certificate verification failed'));

    $response = $this->actingAs(advertisementAdmin(), 'admin')->post(route('admin.ads.store'), [
        'title' => 'Resilient website advertisement',
        'display_type' => 'site',
        'placements' => ['home'],
        'media_type' => 'image',
        'link_url' => 'https://example.com/campaign',
        'advertiser_email' => 'owner@example.com',
        'starts_at' => now()->subMinute()->format('Y-m-d H:i:s'),
        'expires_at' => now()->addMonth()->format('Y-m-d H:i:s'),
        'target_all_categories' => '1',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.ads.index'))
        ->assertSessionHas('success')
        ->assertSessionHas('warning');

    $this->assertDatabaseHas('ads', [
        'title' => 'Resilient website advertisement',
        'activation_notified_at' => null,
    ]);
});
