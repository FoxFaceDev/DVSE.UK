<?php

use App\Models\MockTestHistory;
use App\Models\Choice;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('a new account requires email verification', function () {
    Notification::fake();

    $response = $this->post(route('register'), [
        'name' => 'Test Driver',
        'email' => 'driver@example.com',
        'password' => 'safe-password1',
        'password_confirmation' => 'safe-password1',
    ]);

    $user = User::where('email', 'driver@example.com')->firstOrFail();

    $response->assertRedirect(route('verification.notice'));
    $this->assertAuthenticatedAs($user, 'web');
    expect($user->email_verified_at)->toBeNull();
    Notification::assertSentTo($user, VerifyEmail::class);
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
    $question = Question::create([
        'category_id' => $category->id,
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
