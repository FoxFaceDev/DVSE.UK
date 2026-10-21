<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="color-scheme" content="light">
  <meta name="theme-color" content="#0a3bb8">
  <title>{{ $section?->name ?? 'Theory Test' }} – RIVEX.UK</title>
  <meta name="description" content="Choose an admin-managed RIVEX learning area.">
  <link rel="icon" href="{{ asset('design-v2/Home/img/logo.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('design-v2/Home/fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('design-v2/theory-index.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/js/app.js')
  <link rel="stylesheet" href="{{ asset('design-v2/ui.css') }}">
</head>
<body>
  <header class="hero">
    <img class="hero__photo" src="{{ asset('design-v2/Home/img/hero-photo.jpg') }}" alt="" width="1200" height="830" fetchpriority="high">
    <div class="hero__inner">
      <span class="hero__grid" aria-hidden="true"></span>
      <a class="brand" href="{{ route('home') }}" aria-label="RIVEX.UK – Drive Your Future">
        <img class="brand__icon" src="{{ asset('design-v2/Home/img/logo-icon.png') }}" alt="" width="830" height="696">
        <span class="brand__text">
          <span class="brand__name">RIVE<span class="brand__x">X</span><span class="brand__uk">.UK</span></span>
          <span class="brand__tagline">Drive your future</span>
        </span>
      </a>
    </div>
    <span class="hero__waves" aria-hidden="true"></span>
    <span class="hero__pills" aria-hidden="true"><span class="hero__pill hero__pill--blue"></span><span class="hero__pill hero__pill--gold"></span></span>
  </header>

  <main class="main">
    <section class="greeting" aria-labelledby="greeting-title">
      <h1 id="greeting-title" class="greeting__title">Hi <span class="greeting__name">{{ auth('web')->check() ? Str::before(auth('web')->user()->name, ' ').',' : 'there,' }}</span></h1>
      <p class="greeting__text">Learn. Practise. Drive with confidence.</p>
    </section>
    <div class="stage">
      <ul class="cards cards--managed">
        @foreach($section?->subSections ?? [] as $subSection)
          @php
            $name = Str::lower($subSection->name);
            $cardTone = match (true) {
                Str::contains($name, ['hazard', 'mock']) => 'gold',
                Str::contains($name, ['highway', 'code']) => 'cyan',
                Str::contains($name, 'sign') => 'red',
                default => 'blue',
            };
            $fallbackIcon = match (true) {
                Str::contains($name, 'hazard') => asset('images/rivex/icon-hazard.svg'),
                Str::contains($name, 'sign') => asset('images/rivex/icon-signs.svg'),
                Str::contains($name, ['highway', 'code']) => asset('images/rivex/icon-highway.svg'),
                Str::contains($name, ['perk', 'offer']) => asset('images/rivex/icon-perks.svg'),
                default => asset('images/rivex/icon-theory.svg'),
            };
          @endphp
          <li>
            <a class="card card--{{ $cardTone }}" href="{{ $subSection->isRevisionPractice() ? route('v2.revision') : route('frontend.sub_section', $subSection) }}" style="--admin-card-color: {{ $subSection->color ?: $section?->color ?: '#1664f5' }}">
              <img class="card__icon" src="{{ $subSection->icon_path ?: $fallbackIcon }}" alt="" width="820" height="700">
              <span class="card__body"><span class="card__title">{{ $subSection->name }}</span></span>
              @if($subSection->description)<span class="sr-only">{{ $subSection->description }}</span>@endif
            </a>
          </li>
        @endforeach
        @foreach($section?->topics ?? [] as $topic)
          @php($topicHref = $topic->isLearningCategory() && $topic->cgi_content_pages_count ? route('theory.hazard_library', $topic) : route('theory.practice', $topic))
          <li>
            <a class="card card--blue" href="{{ $topicHref }}" style="--admin-card-color: {{ $topic->color ?: $section?->color ?: '#1664f5' }}">
              <img class="card__icon" src="{{ $topic->icon_path ?: asset('images/rivex/icon-theory.svg') }}" alt="" width="820" height="700">
              <span class="card__body"><span class="card__title">{{ $topic->name_en }}</span></span>
            </a>
          </li>
        @endforeach
        @if(!$section || ($section->subSections->isEmpty() && $section->topics->isEmpty()))
          <li class="v2-empty">No items are available in this section yet.</li>
        @endif
      </ul>
    </div>
  </main>

  <nav class="tabbar" aria-label="Main navigation">
    <ul class="tabbar__list">
      <li><a class="tab tab--active" href="{{ route('home') }}" aria-current="page"><svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16 3.2 3.4 14.1c-.9.8-.4 2.3.8 2.3H6V26c0 1.1.9 2 2 2h4.6v-7h6.8v7H24c1.1 0 2-.9 2-2v-9.6h1.8c1.2 0 1.7-1.5.8-2.3z"/></svg><span class="tab__label">Home</span></a></li>
      <li><a class="tab" href="{{ route('v2.offers') }}"><svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="24" height="5.5" rx="1"/><path d="M6 16.5V28h20V16.5M16 11v17M16 11c-3 0-6.5-1-6.5-4a2.7 2.7 0 0 1 4.9-1.5C15.3 6.8 16 8.6 16 11zM16 11c3 0 6.5-1 6.5-4a2.7 2.7 0 0 0-4.9-1.5C16.7 6.8 16 8.6 16 11z"/></g></svg><span class="tab__label">Offers</span></a></li>
      <li><a class="tab" href="#share" data-share><svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" d="M19 5.5 28.5 14 19 22.5v-5.6C11.8 16.9 7.2 19.8 4 26.5c.9-8.6 5.6-14 15-14.8z"/></svg><span class="tab__label">Share</span></a></li>
      <li><a class="tab" href="{{ auth('web')->check() ? route('account.show') : route('login') }}"><svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"><circle cx="16" cy="16" r="13"/><circle cx="16" cy="12.6" r="4.4"/><path d="M7.6 24.6c2-3.4 5-5 8.4-5s6.4 1.6 8.4 5"/></g></svg><span class="tab__label">Profile</span></a></li>
      <li><a class="tab" href="{{ route('v2.more') }}"><svg class="tab__icon" viewBox="0 0 32 32" aria-hidden="true"><g fill="currentColor"><circle cx="5.5" cy="16" r="3"/><circle cx="16" cy="16" r="3"/><circle cx="26.5" cy="16" r="3"/></g></svg><span class="tab__label">More</span></a></li>
    </ul>
  </nav>
</body>
</html>
