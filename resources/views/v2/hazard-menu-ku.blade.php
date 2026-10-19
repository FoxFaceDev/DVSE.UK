<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="color-scheme" content="light">
  <meta name="theme-color" content="#0452bc">
  <title>Hazard Perception – RIVEX.UK</title>
  <meta name="description" content="Hazard perception: help videos, practice clips, mock test, progress and advice. Find a driving instructor near you.">
  <link rel="icon" href="{{ asset('design-v2/Find_a_Driving_Instructor/img/logo.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('design-v2/Find_a_Driving_Instructor/fonts/inter-tight-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="{{ asset('design-v2/Find_a_Driving_Instructor/fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('design-v2/Find_a_Driving_Instructor/css/find-instructor.css') }}">
  <link rel="stylesheet" href="{{ asset('design-v2/Find_a_Driving_Instructor/css/find-instructor-ku.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/js/app.js')
  <link rel="stylesheet" href="{{ asset('design-v2/ui.css') }}">
</head>
<body>
<!--
  DEVELOPER NOTE – banner text by language
  Only the text inside the banner (title, sentence, button label) changes with the app language;
  the page itself, the layout and everything else stays exactly as it is (English, left-to-right).
  - Replace the text of the elements marked data-i18n inside .promo__body.
  - For a right-to-left language set lang/dir on .promo__body only, e.g.
      <div class="promo__body" lang="ckb" dir="rtl">  …  the text block reads right-to-left, nothing moves.
  - The text block has no fixed width per line: longer or shorter text wraps and the banner grows; nothing is clipped.
-->

  <!-- UI glyphs and menu icons (inline sprite, no scripts, no external files) -->
  <svg class="sprite" width="0" height="0" aria-hidden="true" focusable="false">
    <symbol id="i-back" viewBox="0 0 24 24"><path d="M20 12H5M11.5 5.5 5 12l6.5 6.5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-chev" viewBox="0 0 24 24"><path d="m9.5 5.5 6.5 6.5-6.5 6.5" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h13.5M13 6.5l5.5 5.5-5.5 5.5" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-pin" viewBox="0 0 50 70"><path fill="#f7b51c" d="M25 0C11.2 0 0 11 0 24.6 0 43 25 70 25 70s25-27 25-45.4C50 11 38.8 0 25 0z"/><circle cx="25" cy="24" r="9.5" fill="#fff"/></symbol>
    <symbol id="i-car" viewBox="0 0 76 60"><path fill="currentColor" d="M17 6h42c3 0 5.6 1.9 6.6 4.7L71 26c3 1.4 5 4.4 5 7.8V48c0 1.9-1.6 3.4-3.4 3.4H70V57c0 1.7-1.3 3-3 3h-6c-1.7 0-3-1.3-3-3v-5.6H18V57c0 1.7-1.3 3-3 3H9c-1.7 0-3-1.3-3-3v-5.6H3.4C1.6 51.4 0 49.9 0 48V33.8c0-3.4 2-6.4 5-7.8l5.4-15.3C11.4 7.9 14 6 17 6zm1.6 7.5L14.5 25h47l-4.1-11.5zM11.5 33a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9zm53 0a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9z"/></symbol>
    <!-- menu icons -->
    <symbol id="m-guarantee" viewBox="0 0 80 80"><path fill="#f7b51c" d="M22 46 12 66l11-2 5 10 10-20zM58 46l10 20-11-2-5 10-10-20z"/><path fill="#1452d6" d="M40 4 66 13v18c0 16-11 28-26 34C25 59 14 47 14 31V13z"/><path fill="#f7b51c" d="m40 18 4.4 9 9.9 1.4-7.2 7 1.7 9.8-8.8-4.6-8.8 4.6 1.7-9.8-7.2-7 9.9-1.4z"/></symbol>
    <symbol id="m-videos" viewBox="0 0 80 80"><path fill="#1452d6" d="M8 18 70 6l2 10-62 12z"/><path fill="#fff" d="m20 15 8 9 7-1.4-8-9zm15-3 8 9 7-1.4-8-9zm15-3 8 9 7-1.4-8-9z"/><rect x="8" y="30" width="64" height="44" rx="6" fill="#1452d6"/><rect x="18" y="38" width="44" height="28" rx="4" fill="#fff"/><path fill="#f7b51c" d="M34 43v18l15-9z"/></symbol>
    <symbol id="m-clips" viewBox="0 0 80 80"><rect x="4" y="10" width="60" height="46" rx="7" fill="#1452d6"/><path fill="#fff" d="M27 22v22l18-11z"/><path fill="#f7b51c" stroke="#fff" stroke-width="2.5" stroke-linejoin="round" d="m46 40 26 10-11 3 9 12-6 4-9-12-6 9z"/></symbol>
    <symbol id="m-stopwatch" viewBox="0 0 80 80"><rect x="33" y="3" width="14" height="8" rx="3" fill="#1452d6"/><path d="m60 14 6 6" stroke="#1452d6" stroke-width="6" stroke-linecap="round"/><circle cx="40" cy="45" r="27" fill="none" stroke="#1452d6" stroke-width="8"/><path d="M40 29v17h12" fill="none" stroke="#f7b51c" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="m-progress" viewBox="0 0 80 80"><rect x="8" y="50" width="16" height="24" rx="3" fill="#1452d6"/><rect x="31" y="40" width="16" height="34" rx="3" fill="#1452d6"/><rect x="54" y="28" width="16" height="46" rx="3" fill="#1452d6"/><path d="M8 38 52 12" stroke="#f7b51c" stroke-width="7" stroke-linecap="round"/><path fill="#f7b51c" d="M44 4h28v28l-10-8-10 4z"/></symbol>
    <symbol id="m-info" viewBox="0 0 80 80"><circle cx="40" cy="40" r="36" fill="#1452d6"/><circle cx="40" cy="22" r="6" fill="#f7b51c"/><rect x="34" y="33" width="12" height="30" rx="5" fill="#f7b51c"/></symbol>
  </svg>

  <!-- ===== Fixed header: instructors.html bar, back button + page title ===== -->
  <header class="top">
    <div class="appbar">
      <span class="appbar__waves" aria-hidden="true"></span>
      <div class="appbar__row">
      <a class="back" href="{{ route('home') }}" aria-label="Back">
        <svg class="back__icon" aria-hidden="true"><use href="#i-back"/></svg>
      </a>
      <h1 class="appbar__title">Hazard Perception</h1>
      </div>
    </div>
  </header>

  <main class="page">
    <!-- ===== Find an instructor banner ===== -->
    <section class="promo" aria-labelledby="promo-title">
      <span class="promo__bg" aria-hidden="true"></span>
      <img class="promo__art" src="{{ asset('design-v2/Find_a_Driving_Instructor/img/find/girl-480.webp') }}" srcset="{{ asset('design-v2/Find_a_Driving_Instructor/img/find/girl-480.webp') }} 480w, {{ asset('design-v2/Find_a_Driving_Instructor/img/find/girl-960.webp') }} 960w" sizes="(min-width: 900px) 280px, 46vw" alt="" width="480" height="471" decoding="async" fetchpriority="high">
      <span class="promo__front" aria-hidden="true"></span>
      <svg class="promo__pin" aria-hidden="true"><use href="#i-pin"/></svg>
      <svg class="promo__car" aria-hidden="true"><use href="#i-car"/></svg>

      <div class="promo__body" lang="ckb" dir="rtl">
        <h2 class="promo__title" id="promo-title"><span data-i18n="promo.title.before">دۆزینەوەی</span> <span class="promo__hl" data-i18n="promo.title.highlight">مامۆستای لێخورین</span> <span class="promo__nb" data-i18n="promo.title.after">لە نزیک خۆت</span></h2>
        <a class="promo__cta" href="{{ route('instructors.index') }}">
          <span data-i18n="promo.cta">دۆزینەوەی مامۆستا</span>
          <span class="promo__go" aria-hidden="true"><svg><use href="#i-arrow"/></svg></span>
        </a>
      </div>
    </section>

    <!-- ===== Hazard perception menu ===== -->
    <ul class="menu" aria-label="Hazard perception">
      <li>
        <a class="menu__item" href="{{ route('v2.guarantee') }}">
          <span class="menu__icon" aria-hidden="true"><svg><use href="#m-guarantee"/></svg></span>
          <span class="menu__label">Pass Guarantee</span>
          <span class="menu__tag">FREE</span>
        </a>
      </li>
      <li>
        <a class="menu__item" href="{{ route('v2.hazards', ['clips'=>1]) }}">
          <span class="menu__icon" aria-hidden="true"><svg><use href="#m-videos"/></svg></span>
          <span class="menu__label">Help Videos</span>
        </a>
      </li>
      <li>
        <a class="menu__item" href="{{ route('v2.hazards', ['clips'=>1]) }}">
          <span class="menu__icon" aria-hidden="true"><svg><use href="#m-clips"/></svg></span>
          <span class="menu__label">Practise Video Clips</span>
        </a>
      </li>
      <li>
        <a class="menu__item" href="{{ route('theory.hazard_mock_info') }}">
          <span class="menu__icon" aria-hidden="true"><svg><use href="#m-stopwatch"/></svg></span>
          <span class="menu__label">Mock Test</span>
        </a>
      </li>
      <li>
        <a class="menu__item" href="{{ route('v2.progress') }}">
          <span class="menu__icon" aria-hidden="true"><svg><use href="#m-progress"/></svg></span>
          <span class="menu__label">Progress Monitor</span>
        </a>
      </li>
      <li>
        <a class="menu__item" href="{{ route('v2.guidance') }}">
          <span class="menu__icon" aria-hidden="true"><svg><use href="#m-info"/></svg></span>
          <span class="menu__label">Advice and Information</span>
        </a>
      </li>
    </ul>
  </main>

  <!-- ===== Fixed bottom: tab bar (same as instructors.html) ===== -->
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
