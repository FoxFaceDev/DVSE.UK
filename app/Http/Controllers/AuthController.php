<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (! $request->user('web')->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register', [
            'languages' => Language::active()->get(),
            'privacyPolicy' => SiteSetting::valueFor('privacy_policy', 'Please review and accept our privacy policy.'),
        ]);
    }

    /**
     * Handle the registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'is_instructor' => ['required', Rule::in(['yes', 'no'])],
            'preferred_language_id' => [Rule::requiredIf(fn () => $request->input('is_instructor') === 'no'), 'nullable', 'integer', Rule::exists('languages', 'id')->where('is_active', true)],
            'privacy_policy' => ['accepted'],
            'marketing_email_opt_in' => ['nullable', 'boolean'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $marketingOptIn = $request->boolean('marketing_email_opt_in');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'preferred_language_id' => $request->input('is_instructor') === 'no' ? $request->integer('preferred_language_id') : null,
            'privacy_accepted_at' => now(),
            'account_type' => $request->input('is_instructor') === 'yes'
                ? User::ACCOUNT_TYPE_INSTRUCTOR
                : User::ACCOUNT_TYPE_USER,
            'password' => Hash::make($request->password),
            'marketing_email_opt_in' => $marketingOptIn,
            'marketing_email_opted_in_at' => $marketingOptIn ? now() : null,
            'marketing_email_consent_source' => $marketingOptIn ? 'registration' : null,
        ]);

        Auth::guard('web')->login($user);

        event(new Registered($user));

        return redirect()->route('verification.notice');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
