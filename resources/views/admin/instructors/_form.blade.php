@php
    $selectedGearboxes = old('gearboxes', $profile->gearboxes ?? []);
    $selectedLanguages = old('languages', $profile->languages ?? []);
    $selectedDays = old('available_days', $profile->available_days ?? []);
@endphp

@if($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
        <p class="font-bold">Please correct the following:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="text-base font-bold text-slate-900">Account details</h3>
    <div class="mt-5 grid gap-5 md:grid-cols-2">
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Full name</span><input type="text" name="name" required value="{{ old('name', $instructor->name) }}" class="w-full rounded-lg border border-slate-300"></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Email address</span><input type="email" name="email" required value="{{ old('email', $instructor->email) }}" class="w-full rounded-lg border-slate-300"></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Phone number</span><input type="tel" name="phone_number" value="{{ old('phone_number', $instructor->phone_number) }}" class="w-full rounded-lg border border-slate-300"></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Preferred website language</span><select name="preferred_language_id" class="w-full rounded-lg border-slate-300"><option value="">Default</option>@foreach($languages as $language)<option value="{{ $language->id }}" @selected((string) old('preferred_language_id', $instructor->preferred_language_id) === (string) $language->id)>{{ $language->name }}</option>@endforeach</select></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Country</span><input type="text" name="country" value="{{ old('country', $instructor->country) }}" class="w-full rounded-lg border border-slate-300"></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">City</span><input type="text" name="city" value="{{ old('city', $instructor->city) }}" class="w-full rounded-lg border border-slate-300"></label>
        <label class="block md:col-span-2"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Address</span><textarea name="address" rows="2" class="w-full rounded-lg border-slate-300">{{ old('address', $instructor->address) }}</textarea></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $instructor->exists ? 'New password (optional)' : 'Password' }}</span><input type="password" name="password" {{ $instructor->exists ? '' : 'required' }} class="w-full rounded-lg border-slate-300" autocomplete="new-password"></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Confirm password</span><input type="password" name="password_confirmation" {{ $instructor->exists ? '' : 'required' }} class="w-full rounded-lg border-slate-300" autocomplete="new-password"></label>
    </div>
</section>

<section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="text-base font-bold text-slate-900">Public instructor profile</h3>
    <div class="mt-5 grid gap-5 md:grid-cols-2">
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Postcode</span><input type="text" name="postcode" required maxlength="10" value="{{ old('postcode', $profile->postcode) }}" class="w-full rounded-lg border border-slate-300"></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Hourly rate (£)</span><input name="hourly_rate" type="number" required min="1" max="999" step="0.50" value="{{ old('hourly_rate', $profile->hourly_rate) }}" class="w-full rounded-lg border-slate-300"></label>

        <fieldset><legend class="mb-2 text-sm font-semibold text-slate-700">Gearbox</legend><div class="flex flex-wrap gap-4">@foreach(['automatic' => 'Automatic', 'manual' => 'Manual'] as $value => $label)<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="gearboxes[]" value="{{ $value }}" @checked(in_array($value, $selectedGearboxes))>{{ $label }}</label>@endforeach</div></fieldset>
        <fieldset><legend class="mb-2 text-sm font-semibold text-slate-700">Languages spoken</legend><div class="flex flex-wrap gap-4">@foreach(['en'=>'English','ku'=>'Kurdish','ar'=>'Arabic','tr'=>'Turkish'] as $value=>$label)<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="languages[]" value="{{ $value }}" @checked(in_array($value, $selectedLanguages))>{{ $label }}</label>@endforeach</div></fieldset>
        <fieldset class="md:col-span-2"><legend class="mb-2 text-sm font-semibold text-slate-700">Available days</legend><div class="flex flex-wrap gap-4">@foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="available_days[]" value="{{ $day }}" @checked(in_array($day, $selectedDays))>{{ $day }}</label>@endforeach</div></fieldset>

        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Gender</span><select name="gender" class="w-full rounded-lg border-slate-300"><option value="">Prefer not to say</option>@foreach(['female','male','other'] as $gender)<option value="{{ $gender }}" @selected(old('gender', $profile->gender) === $gender)>{{ ucfirst($gender) }}</option>@endforeach</select></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Preferred contact method</span><select name="contact_method" class="w-full rounded-lg border-slate-300"><option value="call" @selected(old('contact_method', $profile->contact_method ?: 'call') === 'call')>Phone call</option><option value="whatsapp" @selected(old('contact_method', $profile->contact_method) === 'whatsapp')>WhatsApp</option></select></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Rating</span><input name="rating" type="number" min="0" max="5" step="0.01" value="{{ old('rating', $profile->rating) }}" class="w-full rounded-lg border-slate-300"></label>
        <label class="block"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Review count</span><input name="review_count" type="number" min="0" value="{{ old('review_count', $profile->review_count ?? 0) }}" class="w-full rounded-lg border-slate-300"></label>
        <label class="block md:col-span-2"><span class="mb-1.5 block text-sm font-semibold text-slate-700">Biography</span><textarea name="bio" rows="5" maxlength="3000" class="w-full rounded-lg border-slate-300">{{ old('bio', $profile->bio) }}</textarea></label>

        <div class="md:col-span-2">
            <span class="mb-1.5 block text-sm font-semibold text-slate-700">Profile photo</span>
            <div class="flex items-center gap-4">
                @if($profile->photo_url)<img src="{{ $profile->photo_url }}" alt="Current instructor profile" class="h-20 w-20 rounded-xl object-cover">@endif
                <div><input name="photo" type="file" accept="image/png,image/jpeg,image/webp" class="block text-sm">@if($profile->photo_url)<label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remove_photo" value="1">Remove current photo</label>@endif</div>
            </div>
        </div>
    </div>
</section>

<section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="text-base font-bold text-slate-900">Directory status</h3>
    <div class="mt-4 grid gap-3 sm:grid-cols-2">
        <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-3"><input type="checkbox" name="email_verified" value="1" @checked(old('email_verified', $instructor->email_verified_at !== null))><span><strong class="block text-sm">Email verified</strong><small class="text-slate-500">Required before publishing</small></span></label>
        <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-3"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $profile->is_published))><span><strong class="block text-sm">Published</strong><small class="text-slate-500">Visible in the public directory</small></span></label>
        <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-3"><input type="checkbox" name="is_approved" value="1" @checked(old('is_approved', $profile->is_approved))><span><strong class="block text-sm">DVSA approved</strong><small class="text-slate-500">Credentials have been checked</small></span></label>
        <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-3"><input type="checkbox" name="is_premium" value="1" @checked(old('is_premium', $profile->is_premium))><span><strong class="block text-sm">Premium listing</strong><small class="text-slate-500">Show premium status</small></span></label>
    </div>
</section>

<div class="flex items-center justify-end gap-3">
    <a href="{{ route('admin.instructors.index') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
    <button class="rounded-lg bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-dark">{{ $submitLabel }}</button>
</div>
