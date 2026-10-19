<?php

namespace App\Http\Controllers;

use App\Models\InstructorProfile;
use App\Models\User;
use App\Support\MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class InstructorController extends Controller
{
    private function locate(string $postcode): ?array
    {
        if ($postcode === '') {
            return null;
        }
        try {
            return Cache::remember('postcode:'.str_replace(' ', '', $postcode), now()->addDays(30), function () use ($postcode) {
                $response = Http::timeout(5)->get('https://api.postcodes.io/postcodes/'.rawurlencode($postcode));
                $result = $response->successful() ? $response->json('result') : null;

                return is_array($result) && is_numeric($result['latitude'] ?? null) && is_numeric($result['longitude'] ?? null) ? $result : null;
            });
        } catch (\Throwable) {
            return null;
        }
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'postcode' => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z0-9 ]+$/'],
            'gearbox' => ['nullable', Rule::in(['all', 'automatic', 'manual'])],
            'lang' => ['nullable', 'array', 'max:10'], 'lang.*' => ['string', Rule::in(['en', 'ku', 'ar', 'tr'])],
            'sort' => ['nullable', Rule::in(['nearest', 'price', 'rating'])],
            'female' => ['nullable', 'boolean'], 'rated' => ['nullable', 'boolean'], 'weekend' => ['nullable', 'boolean'],
        ]);
        $postcode = strtoupper(trim($validated['postcode'] ?? ''));
        $location = $this->locate($postcode);
        $locationError = $postcode && ! $location ? 'We could not locate that postcode. Showing matching postcode areas instead.' : null;
        $profiles = collect();
        if (Schema::hasTable('instructor_profiles')) {
            $query = InstructorProfile::with('user')->where('is_published', true)->whereHas('user', fn ($q) => $q->where('account_type', 'instructor')->whereNotNull('email_verified_at'));
            if ($request->filled('gearbox') && $request->gearbox !== 'all') {
                $query->whereJsonContains('gearboxes', $request->gearbox);
            }
            foreach ($validated['lang'] ?? [] as $language) {
                $query->whereJsonContains('languages', $language);
            }
            if ($request->boolean('female')) {
                $query->where('gender', 'female');
            }
            if ($request->boolean('rated')) {
                $query->where('rating', '>=', 4.5);
            }
            if ($request->boolean('weekend')) {
                $query->where(fn ($q) => $q->whereJsonContains('available_days', 'Sat')->orWhereJsonContains('available_days', 'Sun'));
            }
            if ($postcode && ! $location) {
                $query->where('postcode', 'like', strtok($postcode, ' ').'%');
            }
            $profiles = $query->get()->map(function ($profile) use ($location) {
                $profile->distance = null;
                if ($location && $profile->latitude !== null && $profile->longitude !== null) {
                    $lat = deg2rad($profile->latitude - $location['latitude']);
                    $lon = deg2rad($profile->longitude - $location['longitude']);
                    $a = sin($lat / 2) ** 2 + cos(deg2rad($location['latitude'])) * cos(deg2rad($profile->latitude)) * sin($lon / 2) ** 2;
                    $profile->distance = round(3958.8 * 2 * atan2(sqrt($a), sqrt(max(0, 1 - $a))), 1);
                }

                return $profile;
            });
            if ($location) {
                $profiles = $profiles->filter(fn ($p) => $p->distance !== null && $p->distance <= 30);
            }
            $profiles = match ($validated['sort'] ?? 'nearest') {
                'price' => $profiles->sortBy(fn ($p) => (float) $p->hourly_rate),
                'rating' => $profiles->sortByDesc('rating'),
                default => $profiles->sortBy(fn ($p) => $p->distance ?? PHP_INT_MAX),
            };
        }

        return view('instructors.index', compact('profiles', 'postcode', 'locationError'));
    }

    public function show(User $user)
    {
        $profile = $user->instructorProfile;
        abort_unless($user->isInstructor() && $user->hasVerifiedEmail() && $profile?->is_published, 404);

        return view('instructors.show', compact('profile', 'user'));
    }

    public function edit(Request $request)
    {
        abort_unless($request->user()->isInstructor(), 403);

        return view('instructors.edit', ['profile' => $request->user()->instructorProfile]);
    }

    public function update(Request $request)
    {
        abort_unless($request->user()->isInstructor(), 403);
        $validated = $request->validate([
            'postcode' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z]{1,2}[0-9][A-Za-z0-9]?\s?[0-9][A-Za-z]{2}$/'],
            'hourly_rate' => ['required', 'numeric', 'min:1', 'max:999'],
            'gearboxes' => ['required', 'array', 'min:1', 'max:2'], 'gearboxes.*' => [Rule::in(['automatic', 'manual'])],
            'languages' => ['required', 'array', 'min:1', 'max:4'], 'languages.*' => [Rule::in(['en', 'ku', 'ar', 'tr'])],
            'available_days' => ['required', 'array', 'min:1', 'max:7'], 'available_days.*' => [Rule::in(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])],
            'gender' => ['nullable', Rule::in(['female', 'male', 'other'])],
            'bio' => ['nullable', 'string', 'max:3000'],
            'contact_method' => ['nullable', Rule::in(['call', 'whatsapp'])],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], 'is_published' => ['nullable', 'boolean'],
        ]);
        $validated['postcode'] = strtoupper(trim($validated['postcode']));
        $location = $this->locate($validated['postcode']);
        $validated['latitude'] = $location['latitude'] ?? null;
        $validated['longitude'] = $location['longitude'] ?? null;
        $validated['is_published'] = $request->boolean('is_published');
        $validated['contact_method'] = $validated['contact_method'] ?? 'call';
        if ($validated['is_published']) {
            abort_unless($request->user()->hasVerifiedEmail(), 403, 'Verify your email before publishing your profile.');
            if (! $location) {
                return back()->withInput()->withErrors(['postcode' => 'We could not verify this postcode. Please check it and try again before publishing.']);
            }
            if (! $request->user()->phone_number) {
                return back()->withInput()->withErrors(['postcode' => 'Add a phone number in your account settings before publishing.']);
            }
        }
        unset($validated['photo']);
        $oldPath = $request->user()->instructorProfile?->photo_path;
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = MediaStorage::store($request->file('photo'), 'instructors');
        }
        $request->user()->instructorProfile()->updateOrCreate(['user_id' => $request->user()->id], $validated);
        if (isset($validated['photo_path']) && $oldPath) {
            MediaStorage::delete($oldPath);
        }

        return redirect()->route('instructors.edit')->with('status', 'Instructor profile saved.');
    }
}
