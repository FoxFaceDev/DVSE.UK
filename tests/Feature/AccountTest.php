<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Choice;
use App\Models\MockTestHistory;
use App\Models\Question;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

function registrationContactFields(): array
{
    return [
        'phone_number' => '+44 7700 900123',
        'country' => 'United Kingdom',
        'city' => 'London',
        'address' => '10 Test Street',
    ];
}

test('a new account requires email verification', function () {
    Notification::fake();

    $response = $this->post(route('register'), [
        ...registrationContactFields(),
        'name' => 'Test Driver',
        'email' => 'driver@example.com',
        'is_instructor' => 'no',
        'password' => 'safe-password1',
        'password_confirmation' => 'safe-password1',
    ]);

    $user = User::where('email', 'driver@example.com')->firstOrFail();

    $response->assertRedirect(route('verification.notice'));
    $this->assertAuthenticatedAs($user, 'web');
    expect($user->email_verified_at)->toBeNull();
    expect($user->account_type)->toBe(User::ACCOUNT_TYPE_USER);
    expect($user->phone_number)->toBe('+44 7700 900123')
        ->and($user->country)->toBe('United Kingdom')
        ->and($user->city)->toBe('London')
        ->and($user->address)->toBe('10 Test Street');
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('registration requires contact and address details', function () {
    $this->post(route('register'), [
        'name' => 'Incomplete Driver',
        'email' => 'incomplete@example.com',
        'is_instructor' => 'no',
        'password' => 'safe-password1',
        'password_confirmation' => 'safe-password1',
    ])->assertSessionHasErrors(['phone_number', 'country', 'city', 'address']);
});

test('a new account can register as an instructor', function () {
    Notification::fake();

    $this->post(route('register'), [
        ...registrationContactFields(),
        'name' => 'Driving Instructor',
        'email' => 'instructor@example.com',
        'is_instructor' => 'yes',
        'password' => 'safe-password1',
        'password_confirmation' => 'safe-password1',
    ])->assertRedirect(route('verification.notice'));

    $instructor = User::where('email', 'instructor@example.com')->firstOrFail();
    expect($instructor->account_type)->toBe(User::ACCOUNT_TYPE_INSTRUCTOR)
        ->and($instructor->isInstructor())->toBeTrue();
});

test('a user can explicitly consent to marketing during registration', function () {
    Notification::fake();

    $this->post(route('register'), [
        ...registrationContactFields(),
        'name' => 'Marketing Subscriber',
        'email' => 'subscriber@example.com',
        'is_instructor' => 'no',
        'marketing_email_opt_in' => '1',
        'password' => 'safe-password1',
        'password_confirmation' => 'safe-password1',
    ])->assertRedirect(route('verification.notice'));

    expect(User::where('email', 'subscriber@example.com')->firstOrFail())
        ->marketing_email_opt_in->toBeTrue()
        ->marketing_email_opted_in_at->not->toBeNull()
        ->marketing_email_consent_source->toBe('registration');
});

test('registration requires an instructor choice', function () {
    $this->post(route('register'), [
        ...registrationContactFields(),
        'name' => 'Test Driver',
        'email' => 'missing-choice@example.com',
        'password' => 'safe-password1',
        'password_confirmation' => 'safe-password1',
    ])->assertSessionHasErrors('is_instructor');

    $this->assertDatabaseMissing('users', ['email' => 'missing-choice@example.com']);
});

test('the admin dashboard reports user and instructor statistics', function () {
    User::factory()->create();
    User::factory()->instructor()->unverified()->create();
    $admin = Admin::create([
        'name' => 'Statistics Admin',
        'email' => 'statistics-admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($admin, 'admin')->get(route('admin.home'))
        ->assertOk()
        ->assertViewHas('userStats', function (array $stats) {
            return $stats['total'] === 2
                && $stats['users'] === 1
                && $stats['instructors'] === 1
                && $stats['verified'] === 1
                && $stats['unverified'] === 1
                && $stats['new_this_month'] === 2;
        })
        ->assertSee('User statistics')
        ->assertSee('Instructor');
});

test('a signed verification link verifies the account', function () {
    $user = User::factory()->unverified()->create();
    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
        'id' => $user->getKey(),
        'hash' => sha1($user->getEmailForVerification()),
    ]);

    $this->actingAs($user, 'web')->get($url)
        ->assertRedirect(route('account.show'));

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('changing an email address clears verification and sends a new link', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->actingAs($user, 'web')->patch(route('account.profile.update'), [
        'name' => 'Updated Driver',
        'email' => 'updated@example.com',
        'current_password' => 'password',
    ])->assertRedirect();

    $user->refresh();
    expect($user->email)->toBe('updated@example.com');
    expect($user->name)->toBe('Updated Driver');
    expect($user->email_verified_at)->toBeNull();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('updating only the name does not require the current password', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')->patch(route('account.profile.update'), [
        'name' => 'A New Name',
        'email' => $user->email,
    ])->assertRedirect();

    expect($user->fresh()->name)->toBe('A New Name');
});

test('a user can update their marketing email preference', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')->patch(route('account.marketing-preferences.update'), [
        'marketing_email_opt_in' => '1',
    ])->assertRedirect()->assertSessionHas('status', 'marketing-subscribed');

    expect($user->fresh())
        ->marketing_email_opt_in->toBeTrue()
        ->marketing_email_opted_in_at->not->toBeNull()
        ->marketing_email_consent_source->toBe('account_settings');

    $this->patch(route('account.marketing-preferences.update'), [
        'marketing_email_opt_in' => '0',
    ])->assertRedirect()->assertSessionHas('status', 'marketing-unsubscribed');

    expect($user->fresh())
        ->marketing_email_opt_in->toBeFalse()
        ->marketing_email_unsubscribed_at->not->toBeNull();
});

test('a user can change their password and delete their account', function () {
    $user = User::factory()->create(['password' => 'old-password1']);
    MockTestHistory::create([
        'user_id' => $user->id,
        'score' => 45,
        'total_questions' => 50,
        'passed' => true,
    ]);

    $this->actingAs($user, 'web')->put(route('account.password.update'), [
        'current_password' => 'old-password1',
        'password' => 'new-password1',
        'password_confirmation' => 'new-password1',
    ])->assertRedirect();

    $this->assertCredentials(['email' => $user->email, 'password' => 'new-password1'], 'web');

    $this->delete(route('account.destroy'), [
        'password' => 'new-password1',
    ])->assertRedirect(route('home'));

    $this->assertGuest('web');
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
    $this->assertDatabaseMissing('mock_test_histories', ['user_id' => $user->id]);
});

test('mock test scores are calculated on the server before history is saved', function () {
    $user = User::factory()->create();
    $category = Category::create(['name_en' => 'Safe Driving', 'name_ku' => null]);
    $topic = \App\Models\Topic::create(['topicable_type' => Category::class, 'topicable_id' => $category->id, 'name_en' => 'Safe Driving Topic']);
    $question = Question::create([
        'topic_id' => $topic->id,
        'text_en' => 'Which choice is correct?',
        'text_ku' => null,
    ]);
    $correctChoice = Choice::create([
        'question_id' => $question->id,
        'text_en' => 'Correct',
        'text_ku' => null,
        'is_correct' => true,
    ]);
    $wrongChoice = Choice::create([
        'question_id' => $question->id,
        'text_en' => 'Wrong',
        'text_ku' => null,
        'is_correct' => false,
    ]);

    $this->actingAs($user, 'web')->postJson(route('theory.mock_test_submit'), [
        'question_ids' => [$question->id],
        'answers' => [$question->id => $correctChoice->id],
    ])->assertOk()->assertJsonStructure(['redirect']);

    $this->assertDatabaseHas('mock_test_histories', [
        'user_id' => $user->id,
        'score' => 1,
        'total_questions' => 1,
    ]);

    $this->get(route('theory.mock_test_result', ['correct' => 50, 'total' => 50]))->assertOk();
    expect(MockTestHistory::where('user_id', $user->id)->count())->toBe(1);

    expect($wrongChoice->is_correct)->toBeFalse();
});
