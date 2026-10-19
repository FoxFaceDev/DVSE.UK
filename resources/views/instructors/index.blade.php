<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="color-scheme" content="light">
  <meta name="theme-color" content="#0452bc">
  <title>Find a Driving Instructor – RIVEX.UK</title>
  <meta name="description" content="Find DVSA approved driving instructors near you: compare prices, languages, gearbox and availability.">
  <link rel="icon" href="{{ asset('design-v2/Instructors/img/logo.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('design-v2/Instructors/img/instructors/logo-icon.webp') }}" as="image">
  <link rel="preload" href="{{ asset('design-v2/Instructors/fonts/inter-tight-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="{{ asset('design-v2/Instructors/fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('design-v2/Instructors/css/instructors.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/js/app.js')
  <link rel="stylesheet" href="{{ asset('design-v2/ui.css') }}">
</head>
<body>

  <!-- UI glyphs and flags (inline sprite, no scripts, no external files) -->
  <svg class="sprite" width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
      <linearGradient id="g-gold" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe08a"/><stop offset=".55" stop-color="#f2b632"/><stop offset="1" stop-color="#c98a12"/></linearGradient>
      <clipPath id="c-uk"><rect width="60" height="40"/></clipPath>
    </defs>
    <!-- page -->
    <symbol id="i-pin" viewBox="0 0 24 24"><path d="M12 21.5s-6.8-6.3-6.8-11.6a6.8 6.8 0 0 1 13.6 0c0 5.3-6.8 11.6-6.8 11.6z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="12" cy="9.8" r="2.5" fill="none" stroke="currentColor" stroke-width="2"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.6" fill="none" stroke="currentColor" stroke-width="2.4"/><path d="m15.8 15.8 4.6 4.6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="currentColor"/><path d="m8.6 8.6 6.8 6.8m0-6.8-6.8 6.8" stroke="#fff" stroke-width="2" stroke-linecap="round"/></symbol>
    <symbol id="i-down" viewBox="0 0 24 24"><path d="m6.5 9.5 5.5 5.5 5.5-5.5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-sort" viewBox="0 0 24 24"><path d="M7.5 20V4.5M4 8l3.5-3.5L11 8M16.5 4v15.5M13 16l3.5 3.5L20 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-verified" viewBox="0 0 24 24"><path fill="#1d6cf2" d="m12 1.8 2.5 1.9 3.1-.3 1 3 2.6 1.7-.9 3 .9 3-2.6 1.7-1 3-3.1-.3-2.5 1.9-2.5-1.9-3.1.3-1-3-2.6-1.7.9-3-.9-3 2.6-1.7 1-3 3.1.3z"/><path d="m8.2 12.2 2.5 2.5 5-5.2" fill="none" stroke="#fff" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-star" viewBox="0 0 24 24"><path fill="#f6b31a" d="m12 2.6 2.9 6 6.5.8-4.8 4.5 1.2 6.5L12 17.2l-5.8 3.2 1.2-6.5-4.8-4.5 6.5-.8z"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="15.5" rx="2.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="M3.5 9.8h17M8 3v4M16 3v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="m8.6 14.6 2.2 2.2 4.4-4.4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-phone" viewBox="0 0 24 24"><path fill="currentColor" d="M6.6 2.8c.6-.2 1.3 0 1.6.6l1.9 3.6c.3.6.2 1.3-.3 1.7L8.3 10a11.6 11.6 0 0 0 5.7 5.7l1.3-1.5c.4-.5 1.1-.6 1.7-.3l3.6 1.9c.6.3.8 1 .6 1.6l-.8 2.4c-.3.9-1.1 1.4-2 1.4C10 21.2 2.8 14 2.8 5.6c0-.9.6-1.7 1.4-2z"/></symbol>
    <symbol id="i-crown" viewBox="0 0 24 24"><path fill="url(#g-gold)" d="m3 8.2 4.6 3.6L12 4.5l4.4 7.3L21 8.2l-1.9 10H4.9z"/><rect x="4.9" y="18.9" width="14.2" height="2.1" rx="1" fill="url(#g-gold)"/></symbol>
    <symbol id="i-trophy" viewBox="0 0 24 24"><path fill="currentColor" d="M7 2.5h10a1 1 0 0 1 1 1V4h2.6a1 1 0 0 1 1 1.1c-.3 3.3-2 5.5-4.5 6.1A6 6 0 0 1 13 14.4V17h2.5a1.5 1.5 0 0 1 1.5 1.5V21H7v-2.5A1.5 1.5 0 0 1 8.5 17H11v-2.6a6 6 0 0 1-4.1-3.2C4.4 10.6 2.7 8.4 2.4 5.1A1 1 0 0 1 3.4 4H6v-.5a1 1 0 0 1 1-1zM4.5 6c.4 1.8 1.3 3 2.4 3.6A10 10 0 0 1 6.2 6H4.5zm15 0h-1.7a10 10 0 0 1-.7 3.6c1.1-.6 2-1.8 2.4-3.6z"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path fill="#1f9d4a" d="M12 2.2 20 5.4v6c0 4.9-3.3 8.9-8 10.4-4.7-1.5-8-5.5-8-10.4v-6z"/><path d="m8.4 12.2 2.5 2.5 4.8-5" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-heart" viewBox="0 0 24 24"><path d="M12 20.3s-7.8-4.6-9.2-9.8C1.9 7 4 4.2 7.2 4.2c2 0 3.5 1.1 4.8 2.9 1.3-1.8 2.8-2.9 4.8-2.9 3.2 0 5.3 2.8 4.4 6.3-1.4 5.2-9.2 9.8-9.2 9.8z" stroke-width="1.9" stroke-linejoin="round"/></symbol>
    <symbol id="i-carf" viewBox="0 0 24 24"><path fill="currentColor" d="M6.9 5.5h10.2c.9 0 1.7.6 2 1.4l1.4 4.1c.9.4 1.5 1.3 1.5 2.3v4.2c0 .6-.5 1-1 1h-.6v1.4c0 .6-.5 1.1-1.1 1.1h-1.5c-.6 0-1.1-.5-1.1-1.1v-1.4H7.3v1.4c0 .6-.5 1.1-1.1 1.1H4.7c-.6 0-1.1-.5-1.1-1.1v-1.4H3c-.5 0-1-.4-1-1v-4.2c0-1 .6-1.9 1.5-2.3l1.4-4.1c.3-.8 1.1-1.4 2-1.4zm.4 2.2-1.1 3.3h11.6l-1.1-3.3zM5.8 13.2a1.4 1.4 0 1 0 0 2.8 1.4 1.4 0 0 0 0-2.8zm12.4 0a1.4 1.4 0 1 0 0 2.8 1.4 1.4 0 0 0 0-2.8z"/></symbol>
    <symbol id="i-tag" viewBox="0 0 24 24"><path fill="currentColor" d="M3 12.6V4.5C3 3.7 3.7 3 4.5 3h8.1c.4 0 .8.2 1.1.4l7 7c.6.6.6 1.5 0 2.1l-8.1 8.1c-.6.6-1.5.6-2.1 0l-7-7c-.3-.2-.5-.6-.5-1zM7.8 6.3a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9.2" fill="none" stroke="currentColor" stroke-width="2.2"/><path d="M12 7v5.2l3.4 2" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-diamond" viewBox="0 0 24 24"><path fill="#bfe1ff" d="M7.2 3.5h9.6L21 9l-9 11.5L3 9z"/><path fill="#7cc2ff" d="M3 9h18l-9 11.5z"/><path d="M3 9h18M8.6 9 12 20.5 15.4 9M7.2 3.5 8.6 9 12 3.5 15.4 9l1.4-5.5" fill="none" stroke="#fff" stroke-width=".9" stroke-linejoin="round"/></symbol>
    <symbol id="i-whatsapp" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2.2a9.7 9.7 0 0 0-8.4 14.6L2.3 21.7l5-1.3A9.7 9.7 0 1 0 12 2.2zm0 1.8a7.9 7.9 0 1 1-4.1 14.7l-.3-.2-2.9.8.8-2.9-.2-.3A7.9 7.9 0 0 1 12 4zm-3.4 3.9c-.2 0-.5 0-.7.3-.3.3-1 1-1 2.4s1 2.8 1.2 3c.1.2 2 3.2 5 4.4 2.5 1 3 .8 3.6.7.5 0 1.7-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.1-.3-.2-.6-.3l-2-1c-.3-.1-.5-.1-.7.2l-.9 1.1c-.2.2-.3.2-.6.1-.3-.2-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.4.1-.6l.5-.5.3-.5c.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5z"/></symbol>
    <symbol id="i-fire" viewBox="0 0 24 24"><path fill="#f47c20" d="M12.6 2.5c.5 3-1.2 4.6-2.7 6.2C8.4 10.3 7 12 7 14.6A5 5 0 0 0 12 19.8a5.3 5.3 0 0 0 5.3-5.4c0-2.3-1.1-3.7-2-4.6.1 1.6-.5 2.8-1.6 3.3.3-3.5-.7-7.4-1.1-10.6z"/><path fill="#ffc53d" d="M12.1 12.4c.2 1.7-1.6 2.3-1.6 4a1.7 1.7 0 0 0 1.8 1.8c1.2 0 2-.9 2-2.2 0-1.5-1.2-2.4-2.2-3.6z"/></symbol>
    <!-- flags (3:2) -->
    <symbol id="f-uk" viewBox="0 0 60 40"><g clip-path="url(#c-uk)"><rect width="60" height="40" fill="#012169"/><path d="M0 0 60 40M60 0 0 40" stroke="#fff" stroke-width="8"/><path d="M0 0 60 40M60 0 0 40" stroke="#c8102e" stroke-width="3"/><path d="M30 0v40M0 20h60" stroke="#fff" stroke-width="12"/><path d="M30 0v40M0 20h60" stroke="#c8102e" stroke-width="7"/></g></symbol>
    <symbol id="f-kurdistan" viewBox="0 0 60 40"><rect width="60" height="40" fill="#fff"/><rect width="60" height="13.4" fill="#ed2024"/><rect y="26.6" width="60" height="13.4" fill="#278e43"/><circle cx="30" cy="20" r="4.6" fill="#febd11"/><path fill="#febd11" d="M30 12.6l1 3.4 2.6-2.4-.8 3.4 3.4-.9-2.4 2.6 3.4 1-3.4 1 2.4 2.6-3.4-.9.8 3.4-2.6-2.4-1 3.4-1-3.4-2.6 2.4.8-3.4-3.4.9 2.4-2.6-3.4-1 3.4-1-2.4-2.6 3.4.9-.8-3.4 2.6 2.4z"/></symbol>
    <symbol id="f-iraq" viewBox="0 0 60 40"><rect width="60" height="40" fill="#fff"/><rect width="60" height="13.4" fill="#ce1126"/><rect y="26.6" width="60" height="13.4" fill="#000"/><path d="M17 23.5h26M19 19.5v4M24.5 17v6.5M30 18.5v5M35.5 17v6.5M41 19.5v4M21.5 17.2h3M32.5 17.2h3" stroke="#007a3d" stroke-width="1.8" stroke-linecap="round"/></symbol>
    <symbol id="f-turkey" viewBox="0 0 60 40"><rect width="60" height="40" fill="#e30a17"/><circle cx="23" cy="20" r="10" fill="#fff"/><circle cx="25.6" cy="20" r="8" fill="#e30a17"/><path fill="#fff" d="m34.3 20 5.9-1.9-3.7 5V16.9l3.7 5z"/></symbol>
  </svg>

  <!-- ===== Fixed header: Home.html brand lock-up (no car) on the quiz-ku.html background + soft 3D waves ===== -->
  <header class="top">
    <div class="appbar">
      <span class="appbar__waves" aria-hidden="true"></span>
      <a class="brand" href="{{ route('home') }}" aria-label="RIVEX.UK – Drive Your Future">
        <img class="brand__icon" src="{{ asset('design-v2/Instructors/img/instructors/logo-icon.webp') }}" alt="" width="240" height="201" fetchpriority="high">
        <span class="brand__text">
          <span class="brand__name">RIVE<span class="brand__x">X</span><span class="brand__uk">.UK</span></span>
          <span class="brand__tagline">Drive your future</span>
        </span>
      </a>
    </div>
  </header>

  <main class="page">
    <h1 class="title">Find a Driving Instructor</h1>

    <!-- ===== Search + filters (plain GET form, no scripts) ===== -->
    <form class="finder" action="{{ route('instructors.index') }}" method="get" role="search" aria-label="Find instructors">
      <div class="search">
        <label class="search__field">
          <svg class="search__pin" aria-hidden="true"><use href="#i-pin"/></svg>
          <span class="sr-only">Postcode</span>
          <input class="search__input" type="text" name="postcode" value="{{ $postcode }}" inputmode="text" autocomplete="postal-code" autocapitalize="characters" spellcheck="false" maxlength="8" pattern="[A-Za-z]{1,2}[0-9][A-Za-z0-9]? ?[0-9][A-Za-z]{2}" title="A UK postcode, for example WV4 4NP">
          <a class="search__clear" href="{{ route('instructors.index') }}" aria-label="Clear postcode"><svg aria-hidden="true"><use href="#i-x"/></svg></a>
        </label>
        <button class="search__btn" type="submit">
          <svg aria-hidden="true"><use href="#i-search"/></svg>
          <span>Search</span>
        </button>
      </div>

      <div class="filters" role="group" aria-label="Filters">
        <label class="chip"><input class="chip__input" type="radio" name="gearbox" value="all" @checked(!request('gearbox') || request('gearbox') === 'all')><span class="chip__face">All</span></label>
        <label class="chip"><input class="chip__input" type="radio" name="gearbox" value="automatic" @checked(request('gearbox') === 'automatic')><span class="chip__face">Automatic</span></label>
        <label class="chip"><input class="chip__input" type="radio" name="gearbox" value="manual" @checked(request('gearbox') === 'manual')><span class="chip__face">Manual</span></label>

        <details class="drop">
          <summary class="chip__face drop__btn">Languages <svg aria-hidden="true"><use href="#i-down"/></svg></summary>
          <div class="drop__panel">
            <label class="check"><input type="checkbox" name="lang[]" value="en" @checked(in_array('en', (array) request('lang', [])))><span>English</span></label>
            <label class="check"><input type="checkbox" name="lang[]" value="ku" @checked(in_array('ku', (array) request('lang', [])))><span>Kurdish</span></label>
            <label class="check"><input type="checkbox" name="lang[]" value="ar" @checked(in_array('ar', (array) request('lang', [])))><span>Arabic</span></label>
            <label class="check"><input type="checkbox" name="lang[]" value="tr" @checked(in_array('tr', (array) request('lang', [])))><span>Turkish</span></label>
            <button class="drop__apply" type="submit">Apply</button>
          </div>
        </details>

        <details class="drop drop--end">
          <summary class="chip__face drop__btn">More <svg aria-hidden="true"><use href="#i-down"/></svg></summary>
          <div class="drop__panel">
            <label class="check"><input type="checkbox" name="female" value="1" @checked(request()->boolean('female'))><span>Female instructor</span></label>
            <label class="check"><input type="checkbox" name="rated" value="1" @checked(request()->boolean('rated'))><span>4.5 stars and above</span></label>
            <label class="check"><input type="checkbox" name="weekend" value="1" @checked(request()->boolean('weekend'))><span>Weekend lessons</span></label>
            <button class="drop__apply" type="submit">Apply</button>
          </div>
        </details>
      </div>

      <div class="results">
        <p class="results__count"><strong>{{ $profiles->count() }} {{ Str::plural('instructor', $profiles->count()) }} found</strong> @if($postcode)<span>near {{ $postcode }}</span>@endif</p>
        <details class="drop drop--end sort">
          <summary class="sort__btn"><svg class="sort__icon" aria-hidden="true"><use href="#i-sort"/></svg>{{ ['nearest'=>'Nearest first','price'=>'Lowest price','rating'=>'Highest rated'][request('sort','nearest')] }}<svg class="sort__chev" aria-hidden="true"><use href="#i-down"/></svg></summary>
          <div class="drop__panel">
            <button class="sort__opt is-current" type="submit" name="sort" value="nearest" aria-current="true">Nearest first</button>
            <button class="sort__opt" type="submit" name="sort" value="price">Lowest price</button>
            <button class="sort__opt" type="submit" name="sort" value="rating">Highest rated</button>
          </div>
        </details>
      </div>
    </form>

    <!-- ===== Instructors ===== -->
    @if($locationError)<p class="v2-notice" role="status">{{ $locationError }}</p>@endif<ul class="list" aria-label="Instructors">@forelse($profiles as $profile)<li><article class="card {{ $profile->is_premium ? 'card--premium' : '' }}" aria-labelledby="instructor-{{ $profile->id }}">
          <div class="avatar {{ $profile->is_premium ? 'avatar--gold' : '' }}">
            @if($profile->photo_url)<img class="avatar__img" src="{{ $profile->photo_url }}" alt="{{ $profile->user->name }}" width="240" height="264" loading="lazy">@else<span class="v2-initials">{{ Str::upper(Str::substr($profile->user->name,0,1)) }}</span>@endif
            @if($profile->is_premium)<span class="avatar__crown" aria-hidden="true"><svg><use href="#i-crown"/></svg></span>@endif
            
          </div>

          <div class="card__info">
            <div class="badges">@if($profile->rating >= 4.5)<p class="badge badge--top"><svg aria-hidden="true"><use href="#i-trophy"/></svg>Top Rated</p>@endif @if($profile->is_approved)<p class="badge badge--dvsa"><svg aria-hidden="true"><use href="#i-shield"/></svg>DVSA Approved</p>@endif @if($profile->is_premium)<p class="badge badge--best"><svg aria-hidden="true"><use href="#i-diamond"/></svg>Premium</p>@endif</div>
            <h2 class="name" id="instructor-{{ $profile->id }}">{{ $profile->user->name }} @if($profile->is_approved)<svg class="name__tick" role="img" aria-label="Verified"><use href="#i-verified"/></svg>@endif</h2>
            <p class="rating">@if($profile->review_count)<svg aria-hidden="true"><use href="#i-star"/></svg><b>{{ number_format($profile->rating, 1) }}</b><span>({{ $profile->review_count }} reviews)</span>@else<span>New instructor</span>@endif</p>
            <p class="where">
              <span><svg aria-hidden="true"><use href="#i-pin"/></svg>{{ $profile->user->city }}</span>
              <span class="where__dot" aria-hidden="true">•</span>
              <span><svg aria-hidden="true"><use href="#i-carf"/></svg>{{ $profile->distance !== null ? $profile->distance.' miles away' : $profile->postcode }}</span>
            </p>
            <ul class="tags" aria-label="Languages">@foreach($profile->languages as $language)<li class="tag">{{ ['en'=>'English','ku'=>'Kurdish','ar'=>'Arabic','tr'=>'Turkish'][$language] ?? $language }}</li>@endforeach</ul>
            <ul class="tags" aria-label="Gearbox">@foreach($profile->gearboxes as $gearbox)<li class="tag"><svg class="tag__icon" aria-hidden="true"><use href="#i-carf"/></svg>{{ ucfirst($gearbox) }}</li>@endforeach</ul>
          </div>

          <div class="price {{ $profile->is_premium ? 'price--gold' : '' }}">
            @if($profile->is_premium)<svg class="price__crown" aria-hidden="true"><use href="#i-crown"/></svg>@endif
            <span class="price__from">From</span>
            <span class="price__value">£{{ number_format($profile->hourly_rate, 0) }}</span>
            <span class="price__unit">/ hour</span>
          </div>

          <label class="fav" title="Save instructor">
            <input class="fav__input" type="checkbox" name="saved" value="{{ $profile->user_id }}" data-instructor-save>
            <svg class="fav__icon" aria-hidden="true"><use href="#i-heart"/></svg>
            <span class="sr-only">Save {{ $profile->user->name }}</span>
          </label>

          <div class="days">
            <p class="days__label"><svg aria-hidden="true"><use href="#i-calendar"/></svg>Usually available:</p>
            <ul class="days__list">@foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)<li class="day {{ in_array($day,$profile->available_days) ? 'is-on' : '' }}">{{ $day }}</li>@endforeach</ul>
          </div>

          <div class="card__cta">
            <a class="btn btn--line" href="{{ route('instructors.show', $profile->user) }}">View Profile</a>
            <a class="btn btn--call" href="{{ $profile->contact_method === 'whatsapp' ? 'https://wa.me/'.preg_replace('/\D/', '', $profile->user->phone_number ?? '') : 'tel:'.preg_replace('/[^+0-9]/', '', $profile->user->phone_number ?? '') }}" aria-label="Call {{ $profile->user->name }}"><svg aria-hidden="true"><use href="{{ $profile->contact_method === 'whatsapp' ? '#i-whatsapp' : '#i-phone' }}"/></svg>{{ $profile->contact_method === 'whatsapp' ? 'WhatsApp' : 'Call' }}</a>
          </div>
        </article></li>@empty<li class="v2-empty">No instructors found. Try another postcode or change your filters.<br><a class="v2-button" href="{{ route('register', ['instructor'=>1]) }}">Instructors — Join RIVEX</a></li>@endforelse</ul></main>

  <!-- ===== Fixed bottom: tab bar (same as Home.html) ===== -->
  <nav class="tabbar" aria-label="Main navigation">
    <ul class="tabbar__list">
      <li>
        <a class="tab tab--active" href="{{ route('home') }}">
          <svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16 3.2 3.4 14.1c-.9.8-.4 2.3.8 2.3H6V26c0 1.1.9 2 2 2h4.6v-7h6.8v7H24c1.1 0 2-.9 2-2v-9.6h1.8c1.2 0 1.7-1.5.8-2.3z"/></svg>
          <span class="tab__label">Home</span>
        </a>
      </li>
      <li>
        <a class="tab" href="{{ route('v2.offers') }}">
          <svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true" focusable="false"><g fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="24" height="5.5" rx="1"/><path d="M6 16.5V28h20V16.5M16 11v17M16 11c-3 0-6.5-1-6.5-4a2.7 2.7 0 0 1 4.9-1.5C15.3 6.8 16 8.6 16 11zM16 11c3 0 6.5-1 6.5-4a2.7 2.7 0 0 0-4.9-1.5C16.7 6.8 16 8.6 16 11z"/></g></svg>
          <span class="tab__label">Offers</span>
        </a>
      </li>
      <li>
        <a class="tab" href="#share" data-share>
          <svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" d="M19 5.5 28.5 14 19 22.5v-5.6C11.8 16.9 7.2 19.8 4 26.5c.9-8.6 5.6-14 15-14.8z"/></svg>
          <span class="tab__label">Share</span>
        </a>
      </li>
      <li>
        <a class="tab" href="{{ auth('web')->check() ? route('account.show') : route('login') }}">
          <svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true" focusable="false"><g fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"><circle cx="16" cy="16" r="13"/><circle cx="16" cy="12.6" r="4.4"/><path d="M7.6 24.6c2-3.4 5-5 8.4-5s6.4 1.6 8.4 5"/></g></svg>
          <span class="tab__label">Profile</span>
        </a>
      </li>
      <li>
        <a class="tab" href="{{ route('v2.more') }}">
          <svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true" focusable="false"><g fill="currentColor"><circle cx="5.5" cy="16" r="3"/><circle cx="16" cy="16" r="3"/><circle cx="26.5" cy="16" r="3"/></g></svg>
          <span class="tab__label">More</span>
        </a>
      </li>
    </ul>
  </nav>

</body>
</html>
