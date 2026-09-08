<x-layouts.admin title="Email Advertisements">
    <div class="mx-auto max-w-5xl">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-900">Send an email advertisement</h3>
            <p class="mt-1 text-sm text-gray-600">Create a branded campaign for verified users who explicitly subscribed to marketing.</p>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 font-medium text-green-700">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-bold">Please correct the following:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.email-advertisements.send') }}" enctype="multipart/form-data" class="space-y-6" onsubmit="return confirm('Send this email advertisement to the selected audience?')">
            @csrf

            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                <p class="font-bold">UK marketing safeguards are active</p>
                <p class="mt-1">Only verified, opted-in users are included. Every email identifies the sender and contains a signed unsubscribe link.</p>
            </div>

            <section class="admin-card rounded-xl border bg-white p-7">
                <h4 class="font-bold text-gray-900">Recipients</h4>
                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                    @foreach([
                        'all' => ['All users', $recipientCounts['all']],
                        'user' => ['Learners', $recipientCounts['user']],
                        'instructor' => ['Instructors', $recipientCounts['instructor']],
                    ] as $value => [$label, $count])
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-300 bg-slate-50 p-4 hover:border-primary">
                            <input type="radio" name="audience" value="{{ $value }}" @checked(old('audience', 'all') === $value) class="h-4 w-4 text-primary focus:ring-primary">
                            <span>
                                <span class="block font-bold text-gray-900">{{ $label }}</span>
                                <span class="text-sm text-gray-500">{{ number_format($count) }} subscribed {{ Str::plural('recipient', $count) }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-6 border-t border-slate-200 pt-5">
                    <input type="hidden" name="language_filter_present" value="1">
                    <h5 class="font-bold text-gray-900">Recipient languages *</h5>
                    <p class="mt-1 text-sm text-gray-500">Only users whose account language is checked will receive this campaign.</p>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                        @foreach($languages as $language)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-300 bg-slate-50 p-4 hover:border-primary">
                                <input type="checkbox" name="language_ids[]" value="{{ $language->id }}" @checked(in_array((string) $language->id, array_map('strval', old('language_ids', $languages->pluck('id')->all())))) class="h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary">
                                <span class="font-semibold text-gray-900">{{ $language->code === 'en' ? 'English only' : $language->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('language_ids')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </section>

            <section class="admin-card rounded-xl border bg-white p-7">
                <h4 class="font-bold text-gray-900">Sender details</h4>
                <p class="mt-1 text-sm text-gray-500">These details appear in the footer of every campaign email.</p>
                <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
                        <label for="business_name" class="mb-1 block text-sm">Business or organisation name *</label>
                        <input id="business_name" name="business_name" type="text" maxlength="150" required value="{{ old('business_name', 'DVSE.UK') }}" class="w-full">
                    </div>
                    <div>
                        <label for="contact_email" class="mb-1 block text-sm">Contact email *</label>
                        <input id="contact_email" name="contact_email" type="email" maxlength="255" required value="{{ old('contact_email', config('mail.from.address')) }}" class="w-full">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="business_address" class="mb-1 block text-sm">Valid business postal address *</label>
                        <textarea id="business_address" name="business_address" rows="3" maxlength="500" required placeholder="Full postal address shown in the email footer" class="w-full">{{ old('business_address') }}</textarea>
                    </div>
                </div>
            </section>

            <section class="admin-card rounded-xl border bg-white p-7">
                <h4 class="font-bold text-gray-900">Email content</h4>
                <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
                        <label for="subject" class="mb-1 block text-sm">Email subject *</label>
                        <input id="subject" name="subject" type="text" maxlength="150" required value="{{ old('subject') }}" placeholder="Example: Get ready for your next test" class="w-full">
                    </div>
                    <div>
                        <label for="headline" class="mb-1 block text-sm">Headline *</label>
                        <input id="headline" name="headline" type="text" maxlength="150" required value="{{ old('headline') }}" placeholder="The main heading inside the email" class="w-full">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="message" class="mb-1 block text-sm">Message *</label>
                        <textarea id="message" name="message" rows="8" maxlength="20000" required placeholder="Write the advertisement message… " class="w-full">{{ old('message') }}</textarea>
                    </div>
                    <div class="lg:col-span-2">
                        <label for="image" class="mb-1 block text-sm">Campaign image</label>
                        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="w-full">
                        <p class="mt-1 text-xs text-gray-500">Optional JPG, PNG or WebP; maximum 5 MB.</p>
                    </div>
                </div>
            </section>

            <section class="admin-card rounded-xl border bg-white p-7">
                <h4 class="font-bold text-gray-900">Call to action</h4>
                <p class="mt-1 text-sm text-gray-500">Optionally add a button that sends readers to your offer.</p>
                <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
                        <label for="button_label" class="mb-1 block text-sm">Button text</label>
                        <input id="button_label" name="button_label" type="text" maxlength="40" value="{{ old('button_label') }}" placeholder="Learn more" class="w-full">
                    </div>
                    <div>
                        <label for="link_url" class="mb-1 block text-sm">Destination URL</label>
                        <input id="link_url" name="link_url" type="url" value="{{ old('link_url') }}" placeholder="https://example.com/offer" class="w-full">
                    </div>
                </div>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex min-h-12 items-center gap-2 rounded-lg bg-primary px-6 py-3 font-bold text-white shadow-sm hover:bg-primary-dark">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-6 9 6-9 6-9-6zm0 0v7l9 4 9-4v-7"/></svg>
                    Send email advertisement
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
