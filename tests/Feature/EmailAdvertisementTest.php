<?php

use App\Mail\AdvertisementEmail;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function emailAdvertisementAdmin(): Admin
{
    return Admin::create([
        'name' => 'Campaign Admin',
        'email' => 'campaign-admin@example.com',
        'password' => bcrypt('password'),
    ]);
}

test('an admin can open the email advertisements page', function () {
    User::factory()->count(2)->create();
    User::factory()->instructor()->create();

    $this->actingAs(emailAdvertisementAdmin(), 'admin')
        ->get(route('admin.email-advertisements.create'))
        ->assertOk()
        ->assertSee('Send an email advertisement')
        ->assertSee('3 recipients');
});

test('an admin can send an advertisement to a selected audience', function () {
    Mail::fake();
    $learners = User::factory()->count(2)->create();
    User::factory()->instructor()->create();

    $this->actingAs(emailAdvertisementAdmin(), 'admin')
        ->post(route('admin.email-advertisements.send'), [
            'audience' => User::ACCOUNT_TYPE_USER,
            'subject' => 'New DVSE offer',
            'headline' => 'Prepare with confidence',
            'message' => 'Our latest learning package is now available.',
            'button_label' => 'View the offer',
            'link_url' => 'https://example.com/offer',
        ])
        ->assertRedirect(route('admin.email-advertisements.create'))
        ->assertSessionHas('success', 'Advertisement email sent to 2 recipients.');

    foreach ($learners as $learner) {
        Mail::assertSent(AdvertisementEmail::class, fn ($mail) => $mail->hasTo($learner->email)
            && $mail->subjectLine === 'New DVSE offer');
    }

    Mail::assertSent(AdvertisementEmail::class, 2);
});

test('an email advertisement requires its core content', function () {
    Mail::fake();

    $this->actingAs(emailAdvertisementAdmin(), 'admin')
        ->post(route('admin.email-advertisements.send'), ['audience' => 'all'])
        ->assertSessionHasErrors(['subject', 'headline', 'message']);

    Mail::assertNothingSent();
});
