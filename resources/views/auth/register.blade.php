<x-layouts.app :showBack="true" :backUrl="route('home')" title="Create account">
    <div class="mx-auto max-w-md">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg shadow-primary/5 sm:p-8">
            <div class="mb-7 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19a4 4 0 0 0-8 0m4-8a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 1v6m3-3h-6"/></svg>
                </div>
                <h1 class="font-heading text-2xl font-bold text-primary-dark">Create your account</h1>
                <p class="mt-2 text-sm text-gray-500">Save your test history and use your chosen study language everywhere.</p>
            </div>

            @if($errors->any())<div role="alert" class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">Please correct the highlighted fields below.</div>@endif

            <form action="{{ route('register') }}" method="POST" x-data="Object.assign(registrationForm('', ''), { instructor: @js(old('is_instructor', 'no')) })" x-init="initPhoneInput()" @submit="prepareRegistrationSubmission()" class="space-y-4">
                @csrf
                <div><label for="name" class="mb-1.5 block text-sm font-semibold text-gray-700">Full name</label><input id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full min-h-12 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 @error('name') border-red-500 @enderror">@error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="email" class="mb-1.5 block text-sm font-semibold text-gray-700">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="w-full min-h-12 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 @error('email') border-red-500 @enderror">@error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="phone_number" class="mb-1.5 block text-sm font-semibold text-gray-700">Phone number</label><input id="phone_number" x-ref="phoneNumber" type="tel" value="{{ old('phone_number') }}" required autocomplete="tel" class="w-full min-h-12 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 @error('phone_number') border-red-500 @enderror"><input x-ref="phoneNumberValue" type="hidden" name="phone_number" value="{{ old('phone_number') }}">@error('phone_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>

                <fieldset>
                    <legend class="text-sm font-semibold text-gray-700">Are you an instructor?</legend>
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        @foreach(['yes' => 'Yes', 'no' => 'No'] as $value => $label)
                            <label class="cursor-pointer"><input type="radio" name="is_instructor" value="{{ $value }}" x-model="instructor" required class="peer sr-only"><span class="flex min-h-12 items-center justify-center rounded-xl border-2 border-gray-200 bg-gray-50 font-semibold peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary">{{ $label }}</span></label>
                        @endforeach
                    </div>
                    @error('is_instructor')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </fieldset>

                <div x-cloak x-show="instructor === 'no'">
                    <label for="preferred_language_id" class="mb-1.5 block text-sm font-semibold text-gray-700">Which language do you want to study with English?</label>
                    <select id="preferred_language_id" name="preferred_language_id" :required="instructor === 'no'" class="w-full min-h-12 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 @error('preferred_language_id') border-red-500 @enderror">
                        <option value="">Select a language</option>
                        @foreach($languages as $language)<option value="{{ $language->id }}" @selected((string) old('preferred_language_id') === (string) $language->id)>{{ $language->name }}</option>@endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">English is always included. You can change this later in Account settings.</p>
                    @error('preferred_language_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div><label for="password" class="mb-1.5 block text-sm font-semibold text-gray-700">Password</label><div class="relative"><input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" class="w-full min-h-12 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 pr-16 @error('password') border-red-500 @enderror"><button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-3 text-sm font-bold text-primary" x-text="showPassword ? 'Hide' : 'Show'"></button></div><p class="mt-1 text-xs text-gray-500">At least 8 characters, including letters and numbers.</p>@error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-gray-700">Confirm password</label><div class="relative"><input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" class="w-full min-h-12 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 pr-16"><button type="button" @click="showConfirmation = !showConfirmation" class="absolute right-3 top-3 text-sm font-bold text-primary" x-text="showConfirmation ? 'Hide' : 'Show'"></button></div></div>

                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/60 p-4"><input type="checkbox" name="marketing_email_opt_in" value="1" @checked(old('marketing_email_opt_in')) class="mt-1 rounded border-gray-300 text-primary"><span><span class="block text-sm font-semibold text-gray-800">Send me DVSE offers and learning updates</span><span class="mt-1 block text-xs leading-5 text-gray-600">Optional. You can unsubscribe at any time.</span></span></label>
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-4"><input type="checkbox" name="privacy_policy" value="1" required @checked(old('privacy_policy')) class="mt-1 rounded border-gray-300 text-primary"><span><span class="block text-sm font-semibold text-gray-800">I agree to the privacy policy</span><span class="mt-1 block whitespace-pre-line text-xs leading-5 text-gray-600">{{ $privacyPolicy }}</span></span></label>
                @error('privacy_policy')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

                <button type="submit" class="w-full min-h-12 rounded-xl bg-primary py-3.5 font-bold text-white shadow-md hover:bg-primary-dark" :disabled="loading"><span x-show="!loading">Create account</span><span x-cloak x-show="loading">Creating account...</span></button>
            </form>
            <div class="mt-7 border-t border-gray-100 pt-6 text-center text-sm text-gray-500">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-primary">Sign in</a></div>
        </div>
    </div>
</x-layouts.app>
