<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InstructorSeeder extends Seeder
{
    use WithoutModelEvents;

    /** Add preview profiles without overwriting subsequent admin edits. */
    public function run(): void
    {
        $englishLanguageId = Language::query()->where('code', 'en')->value('id');

        $instructors = [
            [
                'user' => [
                    'name' => 'Sarah Ahmed (Demo)',
                    'email' => 'sarah.ahmed@rivex.test',
                    'phone_number' => '+447700900101',
                    'country' => 'United Kingdom',
                    'city' => 'Wolverhampton',
                    'address' => 'Wolverhampton',
                ],
                'profile' => [
                    'postcode' => 'WV4 4NP',
                    'latitude' => 52.561576,
                    'longitude' => -2.173988,
                    'hourly_rate' => 34,
                    'gearboxes' => ['automatic'],
                    'languages' => ['en', 'ku'],
                    'available_days' => ['Mon', 'Tue', 'Wed', 'Fri', 'Sat'],
                    'gender' => 'female',
                    'bio' => 'Demo profile for preview purposes, not a real instructor listing. Calm, step-by-step automatic lessons for beginners and nervous learners. Practise junctions and roundabouts at your own pace, with English or Kurdish support.',
                    'photo_path' => 'design-v2/Instructors/img/instructors/sara.webp',
                    'contact_method' => 'call',
                ],
            ],
            [
                'user' => [
                    'name' => 'James Wilson (Demo)',
                    'email' => 'james.wilson@rivex.test',
                    'phone_number' => '+447700900102',
                    'country' => 'United Kingdom',
                    'city' => 'Walsall',
                    'address' => 'Walsall',
                ],
                'profile' => [
                    'postcode' => 'WS1 1TP',
                    'latitude' => 52.586012,
                    'longitude' => -1.981281,
                    'hourly_rate' => 32,
                    'gearboxes' => ['manual'],
                    'languages' => ['en'],
                    'available_days' => ['Mon', 'Tue', 'Thu', 'Fri', 'Sun'],
                    'gender' => 'male',
                    'bio' => 'Demo profile for preview purposes, not a real instructor listing. Structured manual lessons focused on clutch control, parking and busy urban routes. Build independent driving skills with practical test preparation and Sunday lesson options.',
                    'photo_path' => 'design-v2/Instructors/img/instructors/daniel.svg',
                    'contact_method' => 'call',
                ],
            ],
            [
                'user' => [
                    'name' => 'Dilan Karim (Demo)',
                    'email' => 'dilan.karim@rivex.test',
                    'phone_number' => '+447700900103',
                    'country' => 'United Kingdom',
                    'city' => 'Birmingham',
                    'address' => 'Birmingham',
                ],
                'profile' => [
                    'postcode' => 'B1 1BB',
                    'latitude' => 52.480144,
                    'longitude' => -1.902890,
                    'hourly_rate' => 38,
                    'gearboxes' => ['manual', 'automatic'],
                    'languages' => ['en', 'ar', 'tr'],
                    'available_days' => ['Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    'gender' => 'female',
                    'bio' => 'Demo profile for preview purposes, not a real instructor listing. Flexible manual and automatic lessons for returning drivers and test preparation. Refresh city-driving confidence and practise at weekends, with English, Arabic or Turkish support.',
                    'photo_path' => 'design-v2/Instructors/img/instructors/lana.svg',
                    'contact_method' => 'call',
                ],
            ],
        ];

        $created = DB::transaction(function () use ($instructors, $englishLanguageId): int {
            $created = 0;

            foreach ($instructors as $instructor) {
                // Existing accounts and their admin-managed settings belong to the admin.
                if (User::query()->where('email', $instructor['user']['email'])->exists()) {
                    continue;
                }

                $user = new User([
                    ...$instructor['user'],
                    'account_type' => User::ACCOUNT_TYPE_INSTRUCTOR,
                    'preferred_language_id' => $englishLanguageId,
                    'password' => Str::random(64),
                ]);
                // Required for preview publication; no email or notification is sent.
                $user->forceFill(['email_verified_at' => now()])->save();
                $user->instructorProfile()->create([
                    ...$instructor['profile'],
                    'is_published' => true,
                ]);
                $created++;
            }

            return $created;
        });

        $this->command?->info("Created {$created} demo instructors; existing accounts were left unchanged.");
    }
}
