<?php

use App\Mail\AdvertisementEmail;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

function emailAdvertisementAdmin(): Admin
{
    return Admin::create([
        'name' => 'Campaign Admin',
        'email' => 'campaign-admin@example.com',
        'password' => bcrypt('password'),
    ]);
}

test('an admin can open the email advertisements page', function () {
    User::factory()->count(2)->subscribedToMarketing()->create();
    User::factory()->instructor()->subscribedToMarketing()->create();
    User::factory()->create();

    $this->actingAs(emailAdvertisementAdmin(), 'admin')
        ->get(route('admin.email-advertisements.create'))
        ->assertOk()
        ->assertSee('Send an email advertisement')
        ->assertSee('3 subscribed recipients')
        ->assertSee('UK marketing safeguards are active');
});

test('an admin can send an advertisement to a selected audience', function () {
    Mail::fake();
    $learners = User::factory()->count(2)->subscribedToMarketing()->create();
    User::factory()->create();
    User::factory()->instructor()->subscribedToMarketing()->create();

    $this->actingAs(emailAdvertisementAdmin(), 'admin')
        ->post(route('admin.email-advertisements.send'), [
            'audience' => User::ACCOUNT_TYPE_USER,
            'subject' => 'New DVSE offer',
            'headline' => 'Prepare with confidence',
            'message' => 'Our latest learning package is now available.',
            'button_label' => 'View the offer',
            'link_url' => 'https://example.com/offer',
            'business_name' => 'DVSE.UK',
            'business_address' => '1 Example Street, London, SW1A 1AA',
            'contact_email' => 'marketing@dvse.uk',
        ])
        ->assertRedirect(route('admin.email-advertisements.create'))
        ->assertSessionHas('success', 'Advertisement email sent to 2 recipients.');

    foreach ($learners as $learner) {
        Mail::assertSent(AdvertisementEmail::class, fn ($mail) => $mail->hasTo($learner->email)
            && $mail->subjectLine === 'New DVSE offer'
            && str_contains($mail->unsubscribeUrl, '/email/unsubscribe/'));
    }

    Mail::assertSent(AdvertisementEmail::class, 2);
});

test('non-subscribers and unverified subscribers do not receive marketing campaigns', function () {
    Mail::fake();
    $eligible = User::factory()->subscribedToMarketing()->create();
    User::factory()->create();
    User::factory()->unverified()->subscribedToMarketing()->create();
    User::factory()->subscribedToMarketing()->create([
        'marketing_email_unsubscribed_at' => now(),
    ]);

    $this->actingAs(emailAdvertisementAdmin(), 'admin')
        ->post(route('admin.email-advertisements.send'), [
            'audience' => 'all',
            'subject' => 'Subscriber-only campaign',
            'headline' => 'A new learning offer',
            'message' => 'This should only reach eligible subscribers.',
            'business_name' => 'DVSE.UK',
            'business_address' => '1 Example Street, London, SW1A 1AA',
            'contact_email' => 'marketing@dvse.uk',
        ])
        ->assertSessionHas('success', 'Advertisement email sent to 1 recipient.');

    Mail::assertSent(AdvertisementEmail::class, fn ($mail) => $mail->hasTo($eligible->email));
    Mail::assertSent(AdvertisementEmail::class, 1);
});

test('a signed campaign link unsubscribes a user from marketing', function () {
    $user = User::factory()->subscribedToMarketing()->create();
    $url = URL::signedRoute('marketing.unsubscribe.show', ['user' => $user]);

    $this->get($url)
        ->assertOk()
        ->assertSee('Unsubscribe from marketing emails?');

    $this->post($url)->assertRedirect();

    expect($user->fresh())
        ->marketing_email_opt_in->toBeFalse()
        ->marketing_email_unsubscribed_at->not->toBeNull();
});

test('an email advertisement requires its core content', function () {
    Mail::fake();

    $this->actingAs(emailAdvertisementAdmin(), 'admin')
        ->post(route('admin.email-advertisements.send'), ['audience' => 'all'])
        ->assertSessionHasErrors(['subject', 'headline', 'message']);

    Mail::assertNothingSent();
});
