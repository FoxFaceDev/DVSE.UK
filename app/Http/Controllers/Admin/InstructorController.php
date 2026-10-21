<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Models\Language;
use App\Models\User;
use App\Support\MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $instructors = User::query()
            ->where('account_type', User::ACCOUNT_TYPE_INSTRUCTOR)
            ->with('instructorProfile')
            ->when($request->q, function ($query, $term) {
                $query->where(function ($searchQuery) use ($term) {
                    $searchQuery->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone_number', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%")
                        ->orWhereHas('instructorProfile', fn ($profile) => $profile->where('postcode', 'like', "%{$term}%"));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.instructors.index', compact('instructors'));
    }

    public function create()
    {
        return view('admin.instructors.create', [
            'instructor' => new User(['account_type' => User::ACCOUNT_TYPE_INSTRUCTOR]),
            'profile' => new InstructorProfile,
            'languages' => $this->languages(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateInstructor($request);
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = MediaStorage::store($request->file('photo'), 'instructors');
        }

        try {
            $instructor = DB::transaction(function () use ($request, $validated, $photoPath) {
                $user = User::query()->create([
                    ...$validated['user'],
                    'account_type' => User::ACCOUNT_TYPE_INSTRUCTOR,
                    'preferred_language_id' => $validated['preferred_language_id'],
                    'privacy_accepted_at' => now(),
                    'password' => $validated['password'],
                ]);
                $user->forceFill([
                    'email_verified_at' => $request->boolean('email_verified') ? now() : null,
                ])->save();

                $coordinates = $this->locate($validated['profile']['postcode']);
                $profile = $user->instructorProfile()->create([
                    ...$validated['profile'],
                    'latitude' => $coordinates['latitude'] ?? null,
                    'longitude' => $coordinates['longitude'] ?? null,
                    'photo_path' => $photoPath,
                    'is_published' => $request->boolean('is_published'),
                ]);
                $profile->forceFill([
                    'is_approved' => $request->boolean('is_approved'),
                    'is_premium' => $request->boolean('is_premium'),
                    'rating' => $validated['rating'],
                    'review_count' => $validated['review_count'],
                ])->save();

                return $user;
            });
        } catch (\Throwable $exception) {
            if ($photoPath) {
                MediaStorage::delete($photoPath);
            }

            throw $exception;
        }

        return redirect()->route('admin.instructors.edit', $instructor)
            ->with('success', 'Instructor created successfully.');
    }

    public function edit(User $instructor)
    {
        $this->ensureInstructor($instructor);

        return view('admin.instructors.edit', [
            'instructor' => $instructor,
            'profile' => $instructor->instructorProfile ?? new InstructorProfile,
            'languages' => $this->languages(),
        ]);
    }

    public function update(Request $request, User $instructor)
    {
        $this->ensureInstructor($instructor);
        $validated = $this->validateInstructor($request, $instructor);
        $oldPhotoPath = $instructor->instructorProfile?->photo_path;
        $newPhotoPath = null;

        if ($request->hasFile('photo')) {
            $newPhotoPath = MediaStorage::store($request->file('photo'), 'instructors');
        }

        try {
            DB::transaction(function () use ($request, $validated, $instructor, $newPhotoPath) {
                $userDetails = [
                    ...$validated['user'],
                    'preferred_language_id' => $validated['preferred_language_id'],
                ];
                if (! empty($validated['password'])) {
                    $userDetails['password'] = $validated['password'];
                }
                $instructor->update($userDetails);
                $instructor->forceFill([
                    'email_verified_at' => $request->boolean('email_verified') ? ($instructor->email_verified_at ?? now()) : null,
                ])->save();

                $currentPostcode = $instructor->instructorProfile?->postcode;
                $coordinates = $currentPostcode !== $validated['profile']['postcode']
                    ? $this->locate($validated['profile']['postcode'])
                    : null;
                $profileDetails = [
                    ...$validated['profile'],
                    'is_published' => $request->boolean('is_published'),
                ];
                if ($coordinates) {
                    $profileDetails['latitude'] = $coordinates['latitude'];
                    $profileDetails['longitude'] = $coordinates['longitude'];
                } elseif ($currentPostcode !== $validated['profile']['postcode']) {
                    $profileDetails['latitude'] = null;
                    $profileDetails['longitude'] = null;
                }
                if ($newPhotoPath) {
                    $profileDetails['photo_path'] = $newPhotoPath;
                } elseif ($request->boolean('remove_photo')) {
                    $profileDetails['photo_path'] = null;
                }

                $profile = $instructor->instructorProfile()->updateOrCreate(
                    ['user_id' => $instructor->id],
                    $profileDetails,
                );
                $profile->forceFill([
                    'is_approved' => $request->boolean('is_approved'),
                    'is_premium' => $request->boolean('is_premium'),
                    'rating' => $validated['rating'],
                    'review_count' => $validated['review_count'],
                ])->save();
            });
        } catch (\Throwable $exception) {
            if ($newPhotoPath) {
                MediaStorage::delete($newPhotoPath);
            }

            throw $exception;
        }

        if (($newPhotoPath || $request->boolean('remove_photo')) && $oldPhotoPath) {
            MediaStorage::delete($oldPhotoPath);
        }

        return back()->with('success', 'Instructor updated successfully.');
    }

    private function validateInstructor(Request $request, ?User $instructor = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($instructor)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'preferred_language_id' => ['nullable', Rule::exists('languages', 'id')],
            'password' => [$instructor ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'postcode' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z]{1,2}[0-9][A-Za-z0-9]?\s?[0-9][A-Za-z]{2}$/'],
            'hourly_rate' => ['required', 'numeric', 'min:1', 'max:999'],
            'gearboxes' => ['required', 'array', 'min:1', 'max:2'],
            'gearboxes.*' => [Rule::in(['automatic', 'manual'])],
            'languages' => ['required', 'array', 'min:1', 'max:4'],
            'languages.*' => [Rule::in(['en', 'ku', 'ar', 'tr'])],
            'available_days' => ['required', 'array', 'min:1', 'max:7'],
            'available_days.*' => [Rule::in(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])],
            'gender' => ['nullable', Rule::in(['female', 'male', 'other'])],
            'bio' => ['nullable', 'string', 'max:3000'],
            'contact_method' => ['required', Rule::in(['call', 'whatsapp'])],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_photo' => ['nullable', 'boolean'],
            'email_verified' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'is_approved' => ['nullable', 'boolean'],
            'is_premium' => ['nullable', 'boolean'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'review_count' => ['nullable', 'integer', 'min:0'],
        ]);

        $publicationErrors = [];
        if ($request->boolean('is_published') && ! $request->boolean('email_verified')) {
            $publicationErrors['email_verified'] = 'Verify the instructor email before publishing the profile.';
        }
        if ($request->boolean('is_published') && empty($validated['phone_number'])) {
            $publicationErrors['phone_number'] = 'A phone number is required for a published instructor.';
        }
        if ($publicationErrors) {
            throw ValidationException::withMessages($publicationErrors);
        }

        return [
            'user' => collect($validated)->only(['name', 'email', 'phone_number', 'country', 'city', 'address'])->all(),
            'preferred_language_id' => $validated['preferred_language_id'] ?? null,
            'password' => $validated['password'] ?? null,
            'profile' => [
                ...collect($validated)->only(['hourly_rate', 'gearboxes', 'languages', 'available_days', 'gender', 'bio', 'contact_method'])->all(),
                'postcode' => strtoupper(trim($validated['postcode'])),
            ],
            'rating' => $validated['rating'] ?? null,
            'review_count' => $validated['review_count'] ?? 0,
        ];
    }

    private function languages()
    {
        return Language::query()->orderBy('sort_order')->orderBy('name')->get();
    }

    private function ensureInstructor(User $instructor): void
    {
        abort_unless($instructor->isInstructor(), 404);
    }

    private function locate(string $postcode): ?array
    {
        try {
            return Cache::remember('postcode:'.str_replace(' ', '', $postcode), now()->addDays(30), function () use ($postcode) {
                $response = Http::timeout(5)->get('https://api.postcodes.io/postcodes/'.rawurlencode($postcode));
                $result = $response->successful() ? $response->json('result') : null;

                return is_array($result) && is_numeric($result['latitude'] ?? null) && is_numeric($result['longitude'] ?? null)
                    ? $result
                    : null;
            });
        } catch (\Throwable) {
            return null;
        }
    }
}
