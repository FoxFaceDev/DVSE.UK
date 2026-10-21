<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

function instructorAdmin(): Admin
{
    return Admin::query()->create([
        'name' => 'Instructor Manager',
        'email' => 'instructors@example.com',
        'password' => 'password',
    ]);
}

function instructorPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Ava Driving',
        'email' => 'ava@example.com',
        'phone_number' => '+447700900555',
        'country' => 'United Kingdom',
        'city' => 'Birmingham',
        'address' => 'Birmingham City Centre',
        'password' => 'StrongPassword123!',
        'password_confirmation' => 'StrongPassword123!',
        'postcode' => 'B1 1AA',
        'hourly_rate' => 35,
        'gearboxes' => ['automatic'],
        'languages' => ['en', 'ku'],
        'available_days' => ['Mon', 'Sat'],
        'gender' => 'female',
        'bio' => 'Patient instructor for learners of all experience levels.',
        'contact_method' => 'whatsapp',
        'email_verified' => 1,
        'is_published' => 1,
        'is_approved' => 1,
        'is_premium' => 1,
        'rating' => 4.8,
        'review_count' => 20,
    ], $overrides);
}

test('admins can open the instructor management pages', function () {
    $admin = instructorAdmin();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.instructors.index'))
        ->assertOk()
        ->assertSee('Instructor directory')
        ->assertSee('Add Instructor');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.instructors.create'))
        ->assertOk()
        ->assertSee('Create instructor')
        ->assertSee('Public instructor profile');
});

test('admins can create a complete instructor account and profile', function () {
    Http::fake(['api.postcodes.io/*' => Http::response([
        'result' => ['latitude' => 52.4795, 'longitude' => -1.9027],
    ])]);
    $admin = instructorAdmin();

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.instructors.store'), instructorPayload());

    $instructor = User::query()->where('email', 'ava@example.com')->firstOrFail();
    $response->assertRedirect(route('admin.instructors.edit', $instructor));
    expect($instructor->isInstructor())->toBeTrue()
        ->and($instructor->hasVerifiedEmail())->toBeTrue()
        ->and(Hash::check('StrongPassword123!', $instructor->password))->toBeTrue()
        ->and($instructor->instructorProfile->is_published)->toBeTrue()
        ->and($instructor->instructorProfile->is_approved)->toBeTrue()
        ->and($instructor->instructorProfile->languages)->toBe(['en', 'ku'])
        ->and($instructor->instructorProfile->latitude)->toBe(52.4795);
});

test('admins can edit instructor account profile and directory status', function () {
    $admin = instructorAdmin();
    $instructor = User::factory()->instructor()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'phone_number' => '+447700900555',
    ]);
    $instructor->instructorProfile()->create([
        'postcode' => 'B1 1AA',
        'hourly_rate' => 30,
        'gearboxes' => ['manual'],
        'languages' => ['en'],
        'available_days' => ['Mon'],
    ]);

    $payload = instructorPayload([
        'name' => 'Updated Instructor',
        'email' => 'updated@example.com',
        'password' => null,
        'password_confirmation' => null,
        'postcode' => 'B1 1AA',
        'hourly_rate' => 42,
        'gearboxes' => ['manual', 'automatic'],
        'is_published' => null,
        'is_premium' => null,
    ]);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.instructors.update', $instructor), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $instructor->refresh();
    expect($instructor->name)->toBe('Updated Instructor')
        ->and($instructor->email)->toBe('updated@example.com')
        ->and((float) $instructor->instructorProfile->hourly_rate)->toBe(42.0)
        ->and($instructor->instructorProfile->gearboxes)->toBe(['manual', 'automatic'])
        ->and($instructor->instructorProfile->is_published)->toBeFalse()
        ->and($instructor->instructorProfile->is_premium)->toBeFalse();
});

test('published instructors require verified email and a phone number', function () {
    $admin = instructorAdmin();

    $this->actingAs($admin, 'admin')
        ->from(route('admin.instructors.create'))
        ->post(route('admin.instructors.store'), instructorPayload([
            'phone_number' => null,
            'email_verified' => null,
        ]))
        ->assertRedirect(route('admin.instructors.create'))
        ->assertSessionHasErrors(['email_verified', 'phone_number']);
});
