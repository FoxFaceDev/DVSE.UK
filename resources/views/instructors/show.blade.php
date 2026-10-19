<x-layouts.app :title="$user->name" :backUrl="route('instructors.index')">
    <article class="v2-panel">
        @if($profile->photo_url)<img class="v2-profile-photo" src="{{ $profile->photo_url }}" alt="{{ $user->name }}">@endif
        <h1 class="text-2xl font-bold my-4">{{ $user->name }}</h1><p>{{ $user->city }} · {{ $profile->postcode }}</p>
        <p class="my-3 text-xl font-bold text-primary">£{{ number_format($profile->hourly_rate, 0) }} / hour</p>
        @if($profile->is_approved)<p class="v2-correct">DVSA Approved</p>@endif
        <p>{{ implode(' · ', array_map('ucfirst', $profile->gearboxes)) }}</p>
        <p>{{ implode(' · ', array_map(fn ($code) => ['en'=>'English','ku'=>'Kurdish','ar'=>'Arabic','tr'=>'Turkish'][$code] ?? $code, $profile->languages)) }}</p>
        <p class="my-4">Usually available: {{ implode(', ', $profile->available_days) }}</p><p class="whitespace-pre-line">{{ $profile->bio }}</p>
        @if($user->phone_number)<a class="v2-button" href="tel:{{ preg_replace('/[^+0-9]/', '', $user->phone_number) }}">Call {{ Str::before($user->name, ' ') }}</a>@endif
        @if($user->phone_number && $profile->contact_method === 'whatsapp')<a class="v2-button v2-button--secondary" href="https://wa.me/{{ preg_replace('/\D/', '', $user->phone_number) }}" target="_blank" rel="noopener">WhatsApp</a>@endif
    </article>
</x-layouts.app>
