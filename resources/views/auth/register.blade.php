<x-layouts.app :showBack="true" :backUrl="route('home')" title="Create account">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl p-5 sm:p-8 shadow-lg shadow-primary/5 border border-gray-100">
            <div class="text-center mb-7">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19a4 4 0 0 0-8 0m4-8a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 1v6m3-3h-6"/></svg>
                </div>
                <h1 class="text-2xl font-heading font-bold text-primary-dark">Create your account</h1>
                <p class="text-gray-500 mt-2 text-sm">Save your mock-test history and keep your learning progress together.</p>
            </div>

            @if($errors->any())
                <div role="alert" class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
                    Please correct the highlighted fields below.
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST"
                  x-data="registrationForm({{ Js::from(old('country', '')) }}, {{ Js::from(old('city', '')) }})"
                  x-init="initPhoneInput(); initLocations()" @submit="loading = true">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Full name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                           class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800 @error('name') border-red-500 @enderror"
                           placeholder="Your name">
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false"
                           class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800 @error('email') border-red-500 @enderror"
                           placeholder="you@example.com">
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1.5 text-xs text-gray-500">We’ll send a verification link to this address.</p>
                </div>

                <div class="mb-4">
                    <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-1.5">Phone number</label>
                    <input id="phone_number" x-ref="phoneNumber" type="tel" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="tel" inputmode="tel"
                           class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800 @error('phone_number') border-red-500 @enderror">
                    @error('phone_number') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="country" class="block text-sm font-semibold text-gray-700 mb-1.5">Country</label>
                        <select id="country" name="country" x-model="countryName" @change="countryChanged()" required autocomplete="country-name"
                                :disabled="locationsLoading || !!locationError"
                                class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('country') border-red-500 @enderror">
                            <option value="" x-text="locationsLoading ? 'Loading countries...' : 'Select a country'"></option>
                            <template x-for="country in countries" :key="country.iso2">
                                <option :value="country.name" x-text="`${country.emoji || ''} ${country.name}`.trim()"></option>
                            </template>
                        </select>
                        <input x-cloak x-show="locationError" :disabled="!locationError" type="text" name="country" x-model="countryName" required autocomplete="country-name"
                               class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('country') border-red-500 @enderror" placeholder="Enter your country">
                        @error('country') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-1.5">City</label>
                        <select id="city" name="city" x-model="cityName" required autocomplete="address-level2"
                                :disabled="!countryName || citiesLoading || !!locationError"
                                class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('city') border-red-500 @enderror">
                            <option value="" x-text="citiesLoading ? 'Loading cities...' : (countryName ? 'Select a city' : 'Select a country first')"></option>
                            <template x-for="city in cities" :key="city">
                                <option :value="city" x-text="city"></option>
                            </template>
                        </select>
                        <input x-cloak x-show="locationError" :disabled="!locationError" type="text" name="city" x-model="cityName" required autocomplete="address-level2"
                               class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('city') border-red-500 @enderror" placeholder="Enter your city">
                        @error('city') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <p x-cloak x-show="locationError" x-text="locationError" class="-mt-2 mb-4 text-xs text-amber-700"></p>
                <p class="-mt-2 mb-4 text-[11px] text-gray-400">
                    Location data by <a href="https://github.com/dr5hn/countries-states-cities-database" target="_blank" rel="noopener noreferrer" class="underline hover:text-primary">Countries States Cities Database</a>.
                </p>

                <div class="mb-5">
                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-1.5">Address</label>
                    <textarea id="address" name="address" rows="3" maxlength="500" required autocomplete="street-address" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none resize-y @error('address') border-red-500 @enderror" placeholder="House number and street address">{{ old('address') }}</textarea>
                    @error('address') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <fieldset class="mb-5">
                    <legend class="block text-sm font-semibold text-gray-700">Are you an instructor?</legend>
                    <p class="mb-2.5 mt-1 text-xs text-gray-500">Choose Yes if you teach or train other drivers.</p>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="is_instructor" value="yes" required class="peer sr-only" @checked(old('is_instructor') === 'yes')>
                            <span class="flex min-h-12 items-center justify-center rounded-xl border-2 border-gray-200 bg-gray-50 px-4 py-3 font-semibold text-gray-700 transition-all peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary peer-focus-visible:ring-2 peer-focus-visible:ring-primary/30">Yes</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="is_instructor" value="no" required class="peer sr-only" @checked(old('is_instructor') === 'no')>
                            <span class="flex min-h-12 items-center justify-center rounded-xl border-2 border-gray-200 bg-gray-50 px-4 py-3 font-semibold text-gray-700 transition-all peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary peer-focus-visible:ring-2 peer-focus-visible:ring-primary/30">No</span>
                        </label>
                    </div>
                    @error('is_instructor') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </fieldset>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password"
                               class="w-full min-h-12 px-4 py-3 pr-14 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800 @error('password') border-red-500 @enderror"
                               placeholder="Create a password">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 -translate-y-1/2 min-w-10 min-h-10 text-xs font-semibold text-primary rounded-lg hover:bg-primary/10" :aria-label="showPassword ? 'Hide password' : 'Show password'" x-text="showPassword ? 'Hide' : 'Show'"></button>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500">Use at least 8 characters, including letters and numbers.</p>
                    @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-7">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm password</label>
                    <div class="relative">
                        <input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                               class="w-full min-h-12 px-4 py-3 pr-14 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none text-gray-800"
                               placeholder="Repeat your password">
                        <button type="button" @click="showConfirmation = !showConfirmation" class="absolute right-2 top-1/2 -translate-y-1/2 min-w-10 min-h-10 text-xs font-semibold text-primary rounded-lg hover:bg-primary/10" :aria-label="showConfirmation ? 'Hide password confirmation' : 'Show password confirmation'" x-text="showConfirmation ? 'Hide' : 'Show'"></button>
                    </div>
                </div>

                <label class="mb-6 flex cursor-pointer items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                    <input type="checkbox" name="marketing_email_opt_in" value="1" @checked(old('marketing_email_opt_in')) class="mt-1 h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span>
                        <span class="block text-sm font-semibold text-gray-800">Send me DVSE.UK offers and learning updates</span>
                        <span class="mt-1 block text-xs leading-5 text-gray-600">Optional. I agree to receive marketing emails about DVSE.UK learning products, services and promotions. I can unsubscribe at any time.</span>
                    </span>
                </label>

                <button type="submit" class="w-full min-h-12 py-3.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-base shadow-md shadow-primary/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2" :disabled="loading">
                    <span x-show="!loading">Create account</span>
                    <span x-show="loading" style="display: none;">Creating account...</span>
                </button>
            </form>

            <div class="mt-7 pt-6 border-t border-gray-100 text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-primary hover:text-primary-dark">Sign in</a>
            </div>
        </div>
    </div>
</x-layouts.app>
