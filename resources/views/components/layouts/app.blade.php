<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#014cc1">
<title>{{ $title ?? 'Home' }} – RIVEX.UK</title>@vite(['resources/css/app.css','resources/js/app.js'])<link rel="stylesheet" href="{{ asset('design-v2/Theory/css/theory.css') }}">
<link rel="stylesheet" href="{{ asset('design-v2/ui.css') }}">
</head>
<body class="v2-general" x-data="{}">
<a href="#main-content" class="skip-link">Skip to content</a>
<svg class="sprite" width="0" height="0" aria-hidden="true" focusable="false">
    <symbol id="i-back" viewBox="0 0 24 24">
<path d="M20 12H5M11.5 5.5 5 12l6.5 6.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
</symbol>
    <symbol id="i-home" viewBox="0 0 32 32">
<path fill="currentColor" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" d="M16 3.4 4 13.9c-.7.6-.3 1.8.7 1.8H7V26c0 1 .9 1.9 1.9 1.9h3.7V21.200h6.8v6.7h3.7c1 0 1.9-.9 1.9-1.9V15.7h2.3c1 0 1.4-1.200.7-1.8z"/>
</symbol>
    <symbol id="i-gift" viewBox="0 0 32 32">
<g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
<rect x="4" y="11" width="24" height="5.5" rx="1"/>
<path d="M6 16.5V28h20V16.5M16 11v17M16 11c-3 0-6.5-1-6.5-4a2.7 2.7 0 0 1 4.9-1.5C15.3 6.8 16 8.6 16 11zM16 11c3 0 6.5-1 6.5-4a2.7 2.7 0 0 0-4.9-1.5C16.7 6.8 16 8.6 16 11z"/>
</g>
</symbol>
    <symbol id="i-board" viewBox="0 0 32 32">
<path d="M4.5 28V15.5h7V4.5h9V20h7v8zM11.5 15.5V28M20.5 20v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</symbol>
    <symbol id="i-share" viewBox="0 0 32 32">
<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 5.5 28.5 14 19 22.5v-5.6C11.8 16.9 7.200 19.8 4 26.5c.9-8.6 5.6-14 15-14.8z"/>
</symbol>
    <symbol id="i-profile" viewBox="0 0 32 32">
<g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
<circle cx="16" cy="16" r="13"/>
<circle cx="16" cy="12.6" r="4.4"/>
<path d="M7.6 24.6c2-3.4 5-5 8.4-5s6.4 1.6 8.4 5"/>
</g>
</symbol>
    <symbol id="i-more" viewBox="0 0 32 32">
<g fill="currentColor">
<circle cx="5.5" cy="16" r="2.8"/>
<circle cx="16" cy="16" r="2.8"/>
<circle cx="26.5" cy="16" r="2.8"/>
</g>
</symbol>
  </svg>
<header class="top">
    <div class="appbar">
      <div class="appbar__inner">
        <span class="appbar__art" aria-hidden="true">
</span>
        <div class="appbar__row">
        <a class="back" data-back href="{{ $backUrl ?? route('home') }}" aria-label="Back">
          <svg class="back__icon" aria-hidden="true">
<use href="#i-back"/>
</svg>
        </a>
        <h1 class="appbar__title">{{ $title ?? "RIVEX.UK" }}</h1>
        </div>
      </div>
    </div>
  </header>
<main id="main-content" class="v2-content">
@if(session('status'))<div class="v2-notice" role="status">{{ ['verification-link-sent'=>'A fresh verification link has been sent to your email.', 'email-verified'=>'Your email is verified. Welcome to RIVEX.UK!', 'profile-updated'=>'Your account details were updated.', 'profile-updated-verification-sent'=>'Your details were updated. Please verify your new email address.', 'password-updated'=>'Your password was changed successfully.', 'marketing-subscribed'=>'You are now subscribed to RIVEX.UK marketing emails.', 'marketing-unsubscribed'=>'You have unsubscribed from RIVEX.UK marketing emails.', 'account-deleted'=>'Your account has been deleted.'][session('status')] ?? session('status') }}</div>@endif
@if($siteAd ?? null)<aside class="v2-sponsored" aria-label="Sponsored advertisement">
<a href="{{ $siteAd->link_url }}" target="_blank" rel="noopener sponsored">@if($siteAd->media_type === 'video')<video src="{{ $siteAd->media_source }}" autoplay muted loop playsinline controls>
</video>@elseif($siteAd->media_source)<img src="{{ $siteAd->media_source }}" alt="{{ $siteAd->title }}">@endif<span>Sponsored · {{ $siteAd->title }}</span>
</a>
</aside>@endif
{{ $slot }}</main>
<nav class="tabbar" aria-label="Main navigation">
    <ul class="tabbar__list">
      <li>
        <a class="tab tab--active" href="{{ route('home') }}" aria-current="page">
          <svg class="tab__icon" aria-hidden="true">
<use href="#i-home"/>
</svg>
          <span class="tab__label">Home</span>
        </a>
      </li>
      <li>
        <a class="tab" href="{{ route('v2.offers') }}">
          <svg class="tab__icon" aria-hidden="true">
<use href="#i-gift"/>
</svg>
          <span class="tab__label">Offers</span>
        </a>
      </li>
      <li>
        <a class="tab" href="#share" data-share>
          <svg class="tab__icon" aria-hidden="true">
<use href="#i-share"/>
</svg>
          <span class="tab__label">Share</span>
        </a>
      </li>
      <li>
        <a class="tab" href="{{ auth('web')->check() ? route('account.show') : route('login') }}">
          <svg class="tab__icon" aria-hidden="true">
<use href="#i-profile"/>
</svg>
          <span class="tab__label">Profile</span>
        </a>
      </li>
      <li>
        <a class="tab" href="{{ route('v2.more') }}">
          <svg class="tab__icon" aria-hidden="true">
<use href="#i-more"/>
</svg>
          <span class="tab__label">More</span>
        </a>
      </li>
    </ul>
  </nav>
@if($showWhatsappButton ?? false)<a class="v2-whatsapp" href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsappNumber) }}" target="_blank" rel="noopener" aria-label="Chat with Rivex.uk on WhatsApp" data-legacy-label="Chat with DVSE on WhatsApp">WhatsApp</a>@endif
</body>
</html>
