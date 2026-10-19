<?php

use App\Models\Ad;
use App\Models\Admin;
use App\Models\Question;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\Http;

function v2Question(string $text = 'What is a safe gap?'): Question
{
    $section = Section::create(['name' => 'Theory Test']);
    $topic = $section->topics()->create(['name_en' => 'Safety Margins']);
    $question = $topic->questions()->create(['text_en' => $text, 'explanation_en' => 'A safe gap leaves time to stop.']);
    $question->choices()->create(['text_en' => 'Two seconds', 'is_correct' => true]);
    $question->choices()->create(['text_en' => 'No gap', 'is_correct' => false]);

    return $question;
}

test('V2 entry pages render with local design assets and functional routes', function () {
    foreach (['home', 'v2.theory', 'v2.revision', 'v2.revise', 'v2.flagged', 'v2.search', 'v2.progress', 'instructors.index', 'v2.hazards', 'v2.more', 'v2.offers', 'v2.guidance'] as $route) {
        $this->get(route($route))->assertOk()->assertSee('design-v2/', false);
    }
    $this->get(route('home'))->assertSee(route('v2.theory'), false)->assertSee(route('instructors.index'), false)->assertDontSee('Bryar');
});

test('revision can select topics and question search links to the exact matching question', function () {
    $question = v2Question();
    $other = v2Question('How do you approach a roundabout?');
    $this->get(route('v2.revise', ['topics' => [$question->topic_id]]))->assertOk()
        ->assertViewHas('questions', fn ($questions) => $questions->pluck('id')->all() === [$question->id]);
    $this->get(route('v2.search', ['q' => 'safe gap']))->assertOk()->assertSee($question->text_en)->assertDontSee($other->text_en)
        ->assertSee(route('v2.revise', ['question' => $question->id]), false);
    $this->get(route('v2.revise', ['topics' => ['invalid']]))->assertRedirect()->assertSessionHasErrors('topics.0');
});

test('directory applies real distance gearbox language and availability filters', function () {
    Http::fake(['api.postcodes.io/*' => Http::response(['result' => ['latitude' => 52.56, 'longitude' => -2.14]])]);
    $user = User::factory()->create(['account_type' => 'instructor', 'name' => 'Published Instructor']);
    $profile = $user->instructorProfile()->create(['postcode' => 'WV4 4NP', 'latitude' => 52.56, 'longitude' => -2.14, 'hourly_rate' => 32,
        'gearboxes' => ['automatic'], 'languages' => ['en', 'ku'], 'available_days' => ['Sat'], 'gender' => 'female', 'is_published' => true]);
    $this->get(route('instructors.index', ['postcode' => 'WV4 4NP', 'gearbox' => 'automatic', 'lang' => ['ku'], 'weekend' => 1, 'female' => 1]))
        ->assertOk()->assertSee('Published Instructor')->assertSee('0 miles away')->assertSee('£32')->assertDontSee('DVSA Approved');
    $this->get(route('instructors.index', ['gearbox' => 'manual']))->assertOk()->assertDontSee('Published Instructor');
    $profile->update(['is_published' => false]);
    $this->get(route('instructors.show', $user))->assertNotFound();
    $this->get(route('instructors.index'))->assertDontSee('Published Instructor');
});

test('instructor profile publication enforces verification ownership and server controlled badges', function () {
    Http::fake(['api.postcodes.io/*' => Http::response(['result' => ['latitude' => 52.56, 'longitude' => -2.14]])]);
    $user = User::factory()->create(['account_type' => 'instructor', 'phone_number' => '+447700900123']);
    $data = ['postcode' => 'WV4 4NP', 'hourly_rate' => 32, 'gearboxes' => ['manual'], 'languages' => ['en'], 'available_days' => ['Mon'], 'is_published' => 1, 'is_approved' => 1, 'rating' => 5];
    $this->actingAs($user)->put(route('instructors.update'), $data)->assertRedirect(route('instructors.edit'));
    $profile = $user->instructorProfile()->first();
    expect($profile->is_published)->toBeTrue()->and($profile->is_approved)->toBeFalse()->and($profile->rating)->toBeNull();
    $user->forceFill(['email_verified_at' => null])->save();
    $this->actingAs($user)->put(route('instructors.update'), $data)->assertForbidden();
    $learner = User::factory()->create(['account_type' => 'user']);
    $this->actingAs($learner)->get(route('instructors.edit'))->assertForbidden();
    $this->actingAs($learner)->put(route('instructors.update'), $data)->assertForbidden();
});

test('word help is authenticated handles missing configuration and caches generated definitions', function () {
    $question = v2Question();
    $url = route('v2.word-help', $question);
    $this->postJson($url, ['word' => 'safe', 'language' => 'en'])->assertUnauthorized();
    $user = User::factory()->create();
    config(['services.word_help.key' => null]);
    $this->actingAs($user)->postJson($url, ['word' => 'safe', 'language' => 'en'])->assertStatus(503);
    config(['services.word_help.key' => 'test-key', 'services.word_help.model' => 'configured-test-model']);
    Http::fake(['api.openai.com/*' => Http::response([
        'output' => [
            ['type' => 'message', 'content' => [
                ['type' => 'output_text', 'text' => 'Safe means protected from danger.'],
            ]],
        ],
    ])]);
    $this->postJson($url, ['word' => 'safe', 'language' => 'en'])->assertOk()->assertJsonPath('definition', 'Safe means protected from danger.');
    $this->postJson($url, ['word' => 'safe', 'language' => 'en'])->assertOk();
    Http::assertSentCount(1);
    $this->postJson($url, ['word' => 'unrelated', 'language' => 'en'])->assertUnprocessable();
});

test('V2 home preserves targeted site advertisements', function () {
    Ad::create(['title' => 'Local driving offer', 'display_type' => 'site', 'placements' => ['home'], 'is_active' => true, 'media_type' => 'image', 'link_url' => 'https://example.com/local-driving']);
    $this->get(route('home'))->assertOk()->assertSee('Local driving offer')->assertSee('Sponsored advertisement');
});

test('superadmins can moderate instructor directory badges and publication', function () {
    $user = User::factory()->create(['account_type' => 'instructor']);
    $profile = $user->instructorProfile()->create(['postcode' => 'WV4 4NP', 'hourly_rate' => 32, 'gearboxes' => ['manual'], 'languages' => ['en'], 'available_days' => ['Mon']]);
    $admin = Admin::create(['name' => 'Moderator', 'email' => 'moderator@example.com', 'password' => bcrypt('password'), 'is_superadmin' => true]);
    $this->actingAs($admin, 'admin')->put(route('admin.users.instructor-profile.update', $user), ['is_approved' => 1, 'is_premium' => 1, 'is_published' => 1])->assertRedirect();
    expect($profile->fresh()->is_approved)->toBeTrue()->and($profile->fresh()->is_premium)->toBeTrue();
    $this->actingAs($admin, 'admin')->get(route('admin.users.edit', $user))->assertOk()->assertSee('Instructor directory listing');
});
