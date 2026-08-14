<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $histories = $user->mockTestHistories()->latest()->get();

        $stats = [
            'tests' => $histories->count(),
            'passes' => $histories->where('passed', true)->count(),
            'best' => $histories->map(fn ($history) => $history->total_questions > 0
                ? (int) round(($history->score / $history->total_questions) * 100)
                : 0)->max() ?? 0,
        ];

        return view('account.show', [
            'user' => $user,
            'stats' => $stats,
            'recentHistories' => $histories->take(3),
            'languages' => Language::active()->get(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'preferred_language_id' => ['nullable', 'integer', Rule::exists('languages', 'id')->where('is_active', true)],
            'current_password' => [Rule::requiredIf(fn () => $request->input('email') !== $user->email), 'current_password:web'],
        ]);

        $emailChanged = $validated['email'] !== $user->email;

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
            $user->marketing_email_opt_in = false;
            $user->marketing_email_unsubscribed_at = now();
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('status', $emailChanged ? 'profile-updated-verification-sent' : 'profile-updated');
    }

    public function updateMarketingPreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'marketing_email_opt_in' => ['required', 'boolean'],
        ]);
        $user = $request->user();
        $optedIn = (bool) $validated['marketing_email_opt_in'];

        $user->marketing_email_opt_in = $optedIn;

        if ($optedIn) {
            $user->marketing_email_opted_in_at = now();
            $user->marketing_email_unsubscribed_at = null;
            $user->marketing_email_consent_source = 'account_settings';
        } else {
            $user->marketing_email_unsubscribed_at ??= now();
        }

        $user->save();

        return back()->with('status', $optedIn ? 'marketing-subscribed' : 'marketing-unsubscribed');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->session()->regenerate();

        return back()->with('status', 'password-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('deleteAccount', [
            'password' => ['required', 'current_password:web'],
        ]);

        $user = $request->user();

        Auth::guard('web')->logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'account-deleted');
    }
}
