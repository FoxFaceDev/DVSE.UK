<?php

namespace Database\Seeders;

use App\Models\InstructorProfile;
use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstructorSeeder extends Seeder
{
    /**
     * Seed three complete instructor accounts for the public directory.
     */
    public function run(): void
    {
        $englishLanguageId = Language::query()->where('code', 'en')->value('id');

        $instructors = [
            [
                'user' => [
                    'name' => 'Sarah Ahmed',
                    'email' => 'sarah.ahmed@rivex.test',
                    'phone_number' => '+447700900101',
                    'country' => 'United Kingdom',
                    'city' => 'Wolverhampton',
                    'address' => 'Wolverhampton City Centre',
                ],
                'profile' => [
                    'postcode' => 'WV1 1AA',
                    'latitude' => 52.5862,
                    'longitude' => -2.1287,
                    'hourly_rate' => 34,
                    'gearboxes' => ['manual', 'automatic'],
                    'languages' => ['en', 'ar'],
                    'available_days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    'gender' => 'female',
                    'bio' => 'Patient driving instructor helping new and nervous learners build confidence for safe, independent driving.',
                    'contact_method' => 'whatsapp',
                    'is_premium' => true,
                    'rating' => 4.90,
                    'review_count' => 128,
                ],
            ],
            [
                'user' => [
                    'name' => 'James Wilson',
                    'email' => 'james.wilson@rivex.test',
                    'phone_number' => '+447700900102',
                    'country' => 'United Kingdom',
                    'city' => 'Birmingham',
                    'address' => 'Birmingham City Centre',
                ],
                'profile' => [
                    'postcode' => 'B1 1AA',
                    'latitude' => 52.4795,
                    'longitude' => -1.9027,
                    'hourly_rate' => 32,
                    'gearboxes' => ['manual'],
                    'languages' => ['en'],
                    'available_days' => ['Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    'gender' => 'male',
                    'bio' => 'Experienced manual driving instructor offering structured lessons, mock tests, and practical test preparation.',
                    'contact_method' => 'call',
                    'is_premium' => false,
                    'rating' => 4.75,
                    'review_count' => 84,
                ],
            ],
            [
                'user' => [
                    'name' => 'Dilan Karim',
                    'email' => 'dilan.karim@rivex.test',
                    'phone_number' => '+447700900103',
                    'country' => 'United Kingdom',
                    'city' => 'Coventry',
                    'address' => 'Coventry City Centre',
                ],
                'profile' => [
                    'postcode' => 'CV1 1AH',
                    'latitude' => 52.4081,
                    'longitude' => -1.5106,
                    'hourly_rate' => 36,
                    'gearboxes' => ['automatic'],
                    'languages' => ['en', 'ku', 'tr'],
                    'available_days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sun'],
                    'gender' => 'female',
                    'bio' => 'Friendly automatic driving instructor providing multilingual lessons tailored to each learner’s pace.',
                    'contact_method' => 'whatsapp',
                    'is_premium' => true,
                    'rating' => 4.95,
                    'review_count' => 97,
                ],
            ],
        ];

        foreach ($instructors as $instructor) {
            $user = User::query()->updateOrCreate(
                ['email' => $instructor['user']['email']],
                [
                    ...$instructor['user'],
                    'account_type' => User::ACCOUNT_TYPE_INSTRUCTOR,
                    'preferred_language_id' => $englishLanguageId,
                    'privacy_accepted_at' => now(),
                    'password' => 'Password123!',
                ],
            );

            $user->forceFill(['email_verified_at' => now()])->save();

            $profileDetails = $instructor['profile'];
            $administrativeDetails = [
                'is_approved' => true,
                'is_premium' => $profileDetails['is_premium'],
                'rating' => $profileDetails['rating'],
                'review_count' => $profileDetails['review_count'],
            ];

            unset(
                $profileDetails['is_premium'],
                $profileDetails['rating'],
                $profileDetails['review_count'],
            );

            $profile = InstructorProfile::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    ...$profileDetails,
                    'is_published' => true,
                ],
            );

            $profile->forceFill($administrativeDetails)->save();
        }
    }
}
