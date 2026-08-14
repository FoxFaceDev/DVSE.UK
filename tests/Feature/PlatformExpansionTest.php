<?php

use App\Mail\AdActivated;
use App\Mail\AdExpiringSoon;
use App\Models\Ad;
use App\Models\Admin;
use App\Models\Category;
use App\Models\ContentPage;
use App\Models\Language;
use App\Models\MockTest;
use App\Models\Section;
use App\Models\SiteSetting;
use App\Models\SubSection;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

test('registration uses an account language and requires the managed privacy policy', function () {
    Notification::fake();
    SiteSetting::put('privacy_policy', 'A custom policy managed by the admin.');
    $language = Language::where('code', 'ku')->firstOrFail();

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Which language do you want to study with English?')
        ->assertSee('A custom policy managed by the admin.')
        ->assertDontSee('name="country"', false)
        ->assertDontSee('name="city"', false)
        ->assertDontSee('name="address"', false);

    $this->post(route('register'), [
        'name' => 'Language Learner', 'email' => 'language@example.com', 'phone_number' => '+447700900123',
        'is_instructor' => 'no', 'preferred_language_id' => $language->id,
        'password' => 'safe-password1', 'password_confirmation' => 'safe-password1',
    ])->assertSessionHasErrors('privacy_policy');

    $this->post(route('register'), [
        'name' => 'Language Learner', 'email' => 'language@example.com', 'phone_number' => '+447700900123',
        'is_instructor' => 'no', 'preferred_language_id' => $language->id, 'privacy_policy' => '1',
        'password' => 'safe-password1', 'password_confirmation' => 'safe-password1',
    ])->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'language@example.com')->firstOrFail();
    expect($user->preferred_language_id)->toBe($language->id)->and($user->privacy_accepted_at)->not->toBeNull();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('admin can create a dynamic english theory mock test placement', function () {
    $admin = Admin::create(['name' => 'Admin', 'email' => 'mock-admin@example.com', 'password' => 'password']);
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create(['section_id' => $section->id, 'name' => 'Tests']);
    $category = Category::create(['sub_section_id' => $subSection->id, 'name_en' => 'Rules']);
    $topic = Topic::create(['topicable_type' => Category::class, 'topicable_id' => $category->id, 'name_en' => 'Rules']);

    $this->actingAs($admin, 'admin')->post(route('admin.mock-tests.store'), [
        'sub_section_id' => $subSection->id, 'name' => 'English Theory Test', 'type' => 'theory',
        'question_count' => 50, 'video_question_count' => 3, 'duration_minutes' => 57,
        'pass_mark' => 43, 'topic_ids' => [$topic->id], 'is_active' => '1',
    ])->assertRedirect(route('admin.mock-tests.index'));

    $test = MockTest::firstOrFail();
    expect($test->topics)->toHaveCount(1);
    $this->get(route('frontend.sub_section', $subSection))->assertOk()->assertSee('English-only theory test')->assertSee('English Theory Test');
});

test('scheduled ads notify owners and only run in their active window', function () {
    Mail::fake();
    $ad = Ad::create([
        'title' => 'Lifecycle campaign', 'display_type' => 'site', 'placements' => ['home'],
        'media_type' => 'image', 'link_url' => 'https://example.com', 'advertiser_email' => 'owner@example.com',
        'starts_at' => now()->subMinute(), 'expires_at' => now()->addDays(7), 'is_active' => true,
        'targets_all_categories' => true,
    ]);

    $this->artisan('ads:send-lifecycle-notifications')->assertSuccessful();
    Mail::assertSent(AdActivated::class, fn ($mail) => $mail->hasTo('owner@example.com'));
    Mail::assertSent(AdExpiringSoon::class, fn ($mail) => $mail->hasTo('owner@example.com'));
    expect(Ad::currentlyRunning()->whereKey($ad)->exists())->toBeTrue();

    $ad->update(['expires_at' => now()->subMinute()]);
    expect(Ad::currentlyRunning()->whereKey($ad)->exists())->toBeFalse();
});

test('hazard learning library renders filters and clip thumbnails', function () {
    $category = Category::create(['name_en' => 'Hazards']);
    $topic = Topic::create(['topicable_type' => Category::class, 'topicable_id' => $category->id, 'name_en' => 'Hazard videos']);
    $page = ContentPage::create(['topic_id' => $topic->id, 'admin_title' => 'Rural road clip', 'type' => 'cgi_clips']);
    $page->clips()->create(['slot' => 0, 'media_url' => 'https://example.com/hazard.mp4', 'thumbnail_path' => 'https://example.com/thumb.jpg']);
    $page->clips()->create(['slot' => 1, 'media_url' => 'https://example.com/explanation.mp4']);

    $this->get(route('theory.hazard_library', $topic))
        ->assertOk()
        ->assertSee('Latest content')
        ->assertSee('Not watched')
        ->assertSee('Watched')
        ->assertDontSee('Downloaded')
        ->assertDontSee('Choose a clip to study')
        ->assertSee(route('theory.hazard_study', $page));

    $this->get(route('theory.hazard_study', $page))
        ->assertOk()
        ->assertSee('Start hazard clip')
        ->assertSee('See explanation video')
        ->assertSee('hazardWatched', false);
});
