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
                    <div class="relative" @click.away="countryOpen = false">
                        <label id="country-label" class="block text-sm font-semibold text-gray-700 mb-1.5">Country</label>
                        <input type="hidden" name="country" :value="countryName" :disabled="!!locationError">
                        <button type="button" @click="openCountryDropdown()" :disabled="locationsLoading || !!locationError"
                                aria-labelledby="country-label" :aria-expanded="countryOpen.toString()"
                                class="flex w-full min-h-12 items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-left text-gray-800 outline-none transition-all hover:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-60 @error('country') border-red-500 @enderror">
                            <span class="text-xl leading-none" x-text="selectedCountry?.emoji || '🌍'"></span>
                            <span class="min-w-0 flex-1 truncate" x-text="locationsLoading ? 'Loading countries...' : (countryName || 'Select a country')"></span>
                            <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform" :class="countryOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                        </button>
                        <div x-cloak x-show="countryOpen" x-transition.origin.top class="absolute z-50 mt-2 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl shadow-gray-900/10">
                            <div class="border-b border-gray-100 p-2">
                                <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 focus-within:ring-2 focus-within:ring-primary/20">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                                    <input x-ref="countrySearch" x-model="countrySearch" type="search" placeholder="Search countries..." class="min-w-0 flex-1 border-0 bg-transparent px-0 py-2.5 text-sm outline-none focus:ring-0">
                                </div>
                            </div>
                            <div class="max-h-60 overflow-y-auto p-1.5">
                                <template x-for="country in filteredCountries" :key="country.iso2">
                                    <button type="button" @click="selectCountry(country)" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm hover:bg-primary/10 focus:bg-primary/10 focus:outline-none">
                                        <span class="text-xl leading-none" x-text="country.emoji || '🌍'"></span>
                                        <span class="flex-1" x-text="country.name"></span>
                                        <svg x-show="country.name === countryName" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 13 4 4L19 7"/></svg>
                                    </button>
                                </template>
                                <p x-show="filteredCountries.length === 0" class="px-3 py-6 text-center text-sm text-gray-500">No countries found</p>
                            </div>
                        </div>
                        <input x-cloak x-show="locationError" :disabled="!locationError" type="text" name="country" x-model="countryName" required autocomplete="country-name"
                               class="w-full min-h-12 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none @error('country') border-red-500 @enderror" placeholder="Enter your country">
                        @error('country') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="relative" @click.away="cityOpen = false">
                        <label id="city-label" class="block text-sm font-semibold text-gray-700 mb-1.5">City</label>
                        <input type="hidden" name="city" :value="cityName" :disabled="!!locationError">
                        <button type="button" @click="openCityDropdown()" :disabled="!countryName || citiesLoading || !!locationError"
                                aria-labelledby="city-label" :aria-expanded="cityOpen.toString()"
                                class="flex w-full min-h-12 items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-left text-gray-800 outline-none transition-all hover:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-60 @error('city') border-red-500 @enderror">
                            <svg class="h-5 w-5 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"/><circle cx="12" cy="10" r="2"/></svg>
                            <span class="min-w-0 flex-1 truncate" x-text="citiesLoading ? 'Loading cities...' : (cityName || (countryName ? 'Select a city' : 'Select country first'))"></span>
                            <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform" :class="cityOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                        </button>
                        <div x-cloak x-show="cityOpen" x-transition.origin.top class="absolute z-50 mt-2 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl shadow-gray-900/10">
                            <div class="border-b border-gray-100 p-2">
                                <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 focus-within:ring-2 focus-within:ring-primary/20">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                                    <input x-ref="citySearch" x-model="citySearch" type="search" placeholder="Search cities..." class="min-w-0 flex-1 border-0 bg-transparent px-0 py-2.5 text-sm outline-none focus:ring-0">
                                </div>
                            </div>
                            <div class="max-h-60 overflow-y-auto p-1.5">
                                <template x-for="city in filteredCities" :key="city">
                                    <button type="button" @click="selectCity(city)" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm hover:bg-primary/10 focus:bg-primary/10 focus:outline-none">
                                        <span class="flex-1" x-text="city"></span>
                                        <svg x-show="city === cityName" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 13 4 4L19 7"/></svg>
                                    </button>
                                </template>
                                <p x-show="filteredCities.length === 0" class="px-3 py-6 text-center text-sm text-gray-500">No cities found</p>
                                <p x-show="filteredCities.length === 100" class="px-3 py-2 text-center text-xs text-gray-400">Type to narrow the results</p>
                            </div>
                        </div>
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
