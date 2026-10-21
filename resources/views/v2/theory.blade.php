<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="color-scheme" content="light">
  <meta name="theme-color" content="#014cc1">
  <title>{{ $section?->name ?? 'Theory Test' }} – RIVEX.UK</title>
  <meta name="description" content="Theory test practice: revision questions, mock tests, progress tracking and driving guidance.">
  <link rel="icon" href="{{ asset('design-v2/Theory/img/logo.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('design-v2/Theory/fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('design-v2/Theory/css/theory.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/js/app.js')
  <link rel="stylesheet" href="{{ asset('design-v2/ui.css') }}">
</head>
<body>

  <!-- UI glyphs (inline sprite, no scripts) -->
  <svg class="sprite" width="0" height="0" aria-hidden="true" focusable="false">
    <symbol id="i-back" viewBox="0 0 24 24"><path d="M20 12H5M11.5 5.5 5 12l6.5 6.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-home" viewBox="0 0 32 32"><path fill="currentColor" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" d="M16 3.4 4 13.9c-.7.6-.3 1.8.7 1.8H7V26c0 1 .9 1.9 1.9 1.9h3.7V21.200h6.8v6.7h3.7c1 0 1.9-.9 1.9-1.9V15.7h2.3c1 0 1.4-1.200.7-1.8z"/></symbol>
    <symbol id="i-gift" viewBox="0 0 32 32"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="24" height="5.5" rx="1"/><path d="M6 16.5V28h20V16.5M16 11v17M16 11c-3 0-6.5-1-6.5-4a2.7 2.7 0 0 1 4.9-1.5C15.3 6.8 16 8.6 16 11zM16 11c3 0 6.5-1 6.5-4a2.7 2.7 0 0 0-4.9-1.5C16.7 6.8 16 8.6 16 11z"/></g></symbol>
    <symbol id="i-board" viewBox="0 0 32 32"><path d="M4.5 28V15.5h7V4.5h9V20h7v8zM11.5 15.5V28M20.5 20v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-share" viewBox="0 0 32 32"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 5.5 28.5 14 19 22.5v-5.6C11.8 16.9 7.200 19.8 4 26.5c.9-8.6 5.6-14 15-14.8z"/></symbol>
    <symbol id="i-profile" viewBox="0 0 32 32"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="16" cy="16" r="13"/><circle cx="16" cy="12.6" r="4.4"/><path d="M7.6 24.6c2-3.4 5-5 8.4-5s6.4 1.6 8.4 5"/></g></symbol>
    <symbol id="i-more" viewBox="0 0 32 32"><g fill="currentColor"><circle cx="5.5" cy="16" r="2.8"/><circle cx="16" cy="16" r="2.8"/><circle cx="26.5" cy="16" r="2.8"/></g></symbol>
  </svg>

  <!-- ===== Fixed header: back · title · road art ===== -->
  <header class="top">
    <div class="appbar">
      <div class="appbar__inner">
        <span class="appbar__art" aria-hidden="true"></span>
        <div class="appbar__row">
        <a class="back" href="{{ route('home') }}" aria-label="Back">
          <svg class="back__icon" aria-hidden="true"><use href="#i-back"/></svg>
        </a>
        <h1 class="appbar__title">{{ $section?->name ?? 'Theory Test' }}</h1>
        </div>
      </div>
    </div>
  </header>

  <!-- ===== Menu ===== -->
  <main class="menu">
    <ul class="cards cards--managed">
      @foreach($section?->subSections ?? [] as $subSection)
        @php
          $name = Str::lower($subSection->name);
          $fallbackIcon = match (true) {
              Str::contains($name, 'guarantee') => asset('design-v2/Theory/img/theory/pass.svg'),
              Str::contains($name, 'hazard') => asset('images/rivex/icon-hazard.svg'),
              Str::contains($name, 'sign') => asset('images/rivex/icon-signs.svg'),
              Str::contains($name, 'mock') => asset('design-v2/Theory/img/theory/mock.svg'),
              Str::contains($name, 'search') => asset('design-v2/Theory/img/theory/search.svg'),
              Str::contains($name, 'flag') => asset('design-v2/Theory/img/theory/flag.svg'),
              Str::contains($name, 'progress') => asset('design-v2/Theory/img/theory/progress.svg'),
              Str::contains($name, 'partner') => asset('design-v2/Theory/img/theory/partners.svg'),
              Str::contains($name, ['guidance', 'information']) => asset('design-v2/Theory/img/theory/info.svg'),
              default => asset('design-v2/Theory/img/theory/book.svg'),
          };
        @endphp
        <li>
          <a class="card {{ Str::contains($name, 'guarantee') ? 'card--gold' : '' }}" href="{{ $subSection->isRevisionPractice() ? route('v2.revision') : route('frontend.sub_section', $subSection) }}">
            <img class="card__icon" src="{{ $subSection->icon_path ?: $fallbackIcon }}" alt="" width="120" height="100">
            <span class="card__title">{{ $subSection->name }}</span>
            @if($subSection->description)<span class="sr-only">{{ $subSection->description }}</span>@endif
          </a>
        </li>
      @endforeach
      @foreach($section?->topics ?? [] as $topic)
        <li>
          <x-category-card :category="$topic" :href="$topic->isLearningCategory() && $topic->cgi_content_pages_count ? route('theory.hazard_library', $topic) : route('theory.practice', $topic)" :color="$section->color" />
        </li>
      @endforeach
      @if(!$section || ($section->subSections->isEmpty() && $section->topics->isEmpty()))
        <li class="v2-empty">No items are available in this section yet.</li>
      @endif
    </ul>
  </main>

  <!-- ===== Fixed bottom navigation ===== -->
  <nav class="tabbar" aria-label="Main navigation">
    <ul class="tabbar__list">
      <li>
        <a class="tab tab--active" href="{{ route('home') }}" aria-current="page">
          <svg class="tab__icon" aria-hidden="true"><use href="#i-home"/></svg>
          <span class="tab__label">Home</span>
        </a>
      </li>
      <li>
        <a class="tab" href="{{ route('v2.offers') }}">
          <svg class="tab__icon" aria-hidden="true"><use href="#i-gift"/></svg>
          <span class="tab__label">Offers</span>
        </a>
      </li>
      <li>
        <a class="tab" href="#share" data-share>
          <svg class="tab__icon" aria-hidden="true"><use href="#i-share"/></svg>
          <span class="tab__label">Share</span>
        </a>
      </li>
      <li>
        <a class="tab" href="{{ auth('web')->check() ? route('account.show') : route('login') }}">
          <svg class="tab__icon" aria-hidden="true"><use href="#i-profile"/></svg>
          <span class="tab__label">Profile</span>
        </a>
      </li>
      <li>
        <a class="tab" href="{{ route('v2.more') }}">
          <svg class="tab__icon" aria-hidden="true"><use href="#i-more"/></svg>
          <span class="tab__label">More</span>
        </a>
      </li>
    </ul>
  </nav>

</body>
</html>
