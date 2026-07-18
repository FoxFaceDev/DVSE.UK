<?php

use App\Models\Ad;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Section;
use App\Models\SubSection;

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

test('an advertisement can target multiple selected categories', function () {
    [$motorways, $roadSigns, $vehicleSafety] = advertisementCategories();

    $response = $this->actingAs(advertisementAdmin(), 'admin')->post(route('admin.ads.store'), [
        'title' => 'Selected categories ad',
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
        'title' => 'All categories ad',
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
        'title' => 'Editable targeting ad',
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
        'title' => 'Invalid targeting ad',
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
        'title' => 'Road signs only',
        'media_type' => 'image',
        'link_url' => 'https://example.com/road-signs',
        'targets_all_categories' => false,
        'is_active' => true,
    ]);
    $targetedAd->categories()->attach($roadSigns);

    $this->get(route('theory.practice', $motorways))
        ->assertOk()
        ->assertViewHas('ad', fn ($ad) => $ad === null);

    $this->get(route('theory.practice', $roadSigns))
        ->assertOk()
        ->assertViewHas('ad', fn ($ad) => $ad?->is($targetedAd));

    $globalAd = Ad::create([
        'title' => 'Every category',
        'media_type' => 'image',
        'link_url' => 'https://example.com/every-category',
        'targets_all_categories' => true,
        'is_active' => true,
    ]);

    $this->get(route('theory.practice', $motorways))
        ->assertOk()
        ->assertViewHas('ad', fn ($ad) => $ad?->is($globalAd));
});
