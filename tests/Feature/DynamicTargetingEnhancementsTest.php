<?php

use App\Mail\AdvertisementEmail;
use App\Models\Ad;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use App\Models\Section;
use App\Models\SiteSetting;
use App\Models\SubSection;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function targetingEnhancementsAdmin(): Admin
{
    return Admin::create(['name' => 'Targeting Admin', 'email' => 'targeting-admin@example.com', 'password' => bcrypt('password')]);
}

function targetingEnhancementsTopic(string $name): Topic
{
    $section = Section::firstOrCreate(['name' => 'Targeting section']);
    $subSection = SubSection::firstOrCreate(['section_id' => $section->id, 'name' => 'Targeting subsection']);
    $category = Category::create(['sub_section_id' => $subSection->id, 'name_en' => $name.' category']);

    return Topic::create(['topicable_type' => Category::class, 'topicable_id' => $category->id, 'name_en' => $name]);
}

test('question break ads target multiple topics and languages', function () {
    Mail::fake();
    $english = Language::where('code', 'en')->firstOrFail();
    $kurdish = Language::where('code', 'ku')->firstOrFail();
    $signs = targetingEnhancementsTopic('Signs');
    $motorways = targetingEnhancementsTopic('Motorways');

    $this->actingAs(targetingEnhancementsAdmin(), 'admin')->post(route('admin.ads.store'), [
        'title' => 'Topic and language ad',
        'display_type' => 'question',
        'language_ids' => [$english->id, $kurdish->id],
        'media_type' => 'image',
        'link_url' => 'https://example.com/ad',
        'advertiser_email' => 'owner@example.com',
        'starts_at' => now()->subMinute()->format('Y-m-d H:i:s'),
        'expires_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        'target_all_topics' => '0',
        'topic_ids' => [$signs->id, $motorways->id],
        'is_active' => '1',
    ])->assertRedirect(route('admin.ads.index'))->assertSessionHasNoErrors();

    $ad = Ad::where('title', 'Topic and language ad')->firstOrFail();
    expect($ad->topics()->pluck('topics.id')->sort()->values()->all())->toBe([$signs->id, $motorways->id])
        ->and($ad->languages()->pluck('languages.id')->sort()->values()->all())->toBe([$english->id, $kurdish->id]);
});

test('email campaigns only reach users with checked account languages', function () {
    Mail::fake();
    $english = Language::where('code', 'en')->firstOrFail();
    $kurdish = Language::where('code', 'ku')->firstOrFail();
    $englishUser = User::factory()->subscribedToMarketing()->create(['preferred_language_id' => $english->id]);
    User::factory()->subscribedToMarketing()->create(['preferred_language_id' => $kurdish->id]);

    $this->actingAs(targetingEnhancementsAdmin(), 'admin')->post(route('admin.email-advertisements.send'), [
        'audience' => 'all',
        'language_filter_present' => '1',
        'language_ids' => [$english->id],
        'subject' => 'English campaign',
        'headline' => 'English users',
        'message' => 'Language-targeted message.',
        'business_name' => 'DVSE.UK',
        'business_address' => '1 Example Street, London',
        'contact_email' => 'marketing@example.com',
    ])->assertSessionHas('success', 'Advertisement email sent to 1 recipient.');

    Mail::assertSent(AdvertisementEmail::class, fn ($mail) => $mail->hasTo($englishUser->email));
    Mail::assertSent(AdvertisementEmail::class, 1);
});

test('the whatsapp button follows admin page selections and can be hidden everywhere', function () {
    $topic = targetingEnhancementsTopic('WhatsApp topic');
    SiteSetting::put('whatsapp_number', '+447700900000');
    SiteSetting::put('whatsapp_placements', ['categories']);

    $this->get(route('frontend.category', $topic->topicable))->assertSee('Chat with DVSE on WhatsApp');
    $this->get(route('home'))->assertDontSee('Chat with DVSE on WhatsApp');

    SiteSetting::put('whatsapp_placements', []);
    $this->get(route('frontend.category', $topic->topicable))->assertDontSee('Chat with DVSE on WhatsApp');
});

test('registration offers an explicit english only choice', function () {
    $this->get(route('register'))->assertOk()->assertSee('English only');
});
