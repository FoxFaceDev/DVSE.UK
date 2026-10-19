<x-layouts.app title="Your Instructor Profile" :backUrl="route('account.show')">
    <form class="v2-panel max-w-2xl mx-auto" method="post" action="{{ route('instructors.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <h2>Your public instructor profile</h2>
        @if($errors->any())<div class="v2-form-errors" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <p>Only published profiles with a verified email appear in the directory. Your public profile includes your name, city and contact phone number.</p>
        <label class="v2-field"><span>Postcode</span><input name="postcode" value="{{ old('postcode', $profile?->postcode) }}" required autocomplete="postal-code" maxlength="10"></label>
        <label class="v2-field"><span>Hourly rate (£)</span><input name="hourly_rate" type="number" min="1" max="999" step="0.50" value="{{ old('hourly_rate', $profile?->hourly_rate) }}" required></label>
        <fieldset class="v2-field"><legend class="font-bold mb-2">Gearbox</legend>@foreach(['automatic','manual'] as $gearbox)<label class="mr-5 inline-flex items-center gap-2"><input type="checkbox" name="gearboxes[]" value="{{ $gearbox }}" @checked(in_array($gearbox, old('gearboxes', $profile?->gearboxes ?? [])))>{{ ucfirst($gearbox) }}</label>@endforeach</fieldset>
        <fieldset class="v2-field"><legend class="font-bold mb-2">Languages</legend>@foreach(['en'=>'English','ku'=>'Kurdish','ar'=>'Arabic','tr'=>'Turkish'] as $code=>$label)<label class="mr-5 inline-flex items-center gap-2"><input type="checkbox" name="languages[]" value="{{ $code }}" @checked(in_array($code, old('languages', $profile?->languages ?? [])))>{{ $label }}</label>@endforeach</fieldset>
        <fieldset class="v2-field"><legend class="font-bold mb-2">Usually available</legend>@foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)<label class="mr-4 inline-flex items-center gap-2"><input type="checkbox" name="available_days[]" value="{{ $day }}" @checked(in_array($day, old('available_days', $profile?->available_days ?? [])))>{{ $day }}</label>@endforeach</fieldset>
        <label class="v2-field"><span>Gender (optional)</span><select name="gender"><option value="">Prefer not to say</option>@foreach(['female','male','other'] as $gender)<option value="{{ $gender }}" @selected(old('gender', $profile?->gender) === $gender)>{{ ucfirst($gender) }}</option>@endforeach</select></label>
        <label class="v2-field"><span>About you</span><textarea name="bio" rows="5" maxlength="3000">{{ old('bio', $profile?->bio) }}</textarea></label>
        <label class="v2-field"><span>Preferred contact method</span><select name="contact_method"><option value="call" @selected(old('contact_method', $profile?->contact_method) !== 'whatsapp')>Phone call</option><option value="whatsapp" @selected(old('contact_method', $profile?->contact_method) === 'whatsapp')>WhatsApp</option></select></label>
        <label class="v2-field"><span>Profile photo</span><input name="photo" type="file" accept="image/png,image/jpeg,image/webp"></label>
        <label class="flex items-center gap-3 my-5"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $profile?->is_published))>Publish my profile in the instructor directory</label>
        <button class="v2-button">Save profile</button>
    </form>
</x-layouts.app>
