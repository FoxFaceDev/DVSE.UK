@props(['title' => 'Home', 'page' => 'home', 'heading' => null, 'subheading' => 'Learn. Practise. Drive with confidence.'])
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a3bb8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rivex.uk – {{ $title }}</title>
    <meta name="description" content="Learn. Practise. Drive with confidence.">
    <link rel="icon" href="{{ asset('images/rivex/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="rivex-shell rivex-{{ $page }}">
    <a href="#main-content" class="rivex-skip-link">Skip to content</a>
    <header class="rivex-hero">
        <img class="rivex-hero__photo" src="{{ asset('images/rivex/hero-photo.jpg') }}" alt="" width="1200" height="830" fetchpriority="high">
        <div class="rivex-hero__inner">
            <span class="rivex-hero__grid" aria-hidden="true"></span>
            <a class="rivex-brand" href="{{ route('home') }}" aria-label="Rivex.uk – Drive Your Future">
                <img class="rivex-brand__icon" src="{{ asset('images/rivex/logo-icon.png') }}" alt="" width="830" height="696">
                <span class="rivex-brand__text"><span class="rivex-brand__name">RIVE<span class="rivex-brand__x">X</span><span class="rivex-brand__uk">.UK</span></span><span class="rivex-brand__tagline">Drive your future</span></span>
            </a>
        </div>
        <span class="rivex-hero__waves" aria-hidden="true"></span>
        <span class="rivex-hero__pills" aria-hidden="true"><span class="rivex-hero__pill rivex-hero__pill--blue"></span><span class="rivex-hero__pill rivex-hero__pill--gold"></span></span>
    </header>
    <main id="main-content" class="rivex-main">
        <section class="rivex-greeting" aria-labelledby="rivex-greeting-title">
            @if($page === 'home')
                <h1 id="rivex-greeting-title" class="rivex-greeting__title">Hi <span>{{ auth('web')->user()?->name ?? 'there' }},</span></h1>
            @else
                <h1 id="rivex-greeting-title" class="rivex-greeting__title">{{ $heading ?? $title }}</h1>
            @endif
            <p class="rivex-greeting__text">{{ $subheading }}</p>
        </section>
        @if($siteAd ?? null)
            <aside class="rivex-ad" aria-label="Sponsored advertisement"><a href="{{ $siteAd->link_url }}" target="_blank" rel="noopener sponsored">
                @if($siteAd->media_type === 'video')<video src="{{ $siteAd->media_source }}" autoplay muted loop playsinline></video>@elseif($siteAd->media_source)<img src="{{ $siteAd->media_source }}" alt="{{ $siteAd->title }}">@endif
                <span><small>Sponsored</small><strong>{{ $siteAd->title }}</strong></span>
            </a></aside>
        @endif
        {{ $slot }}
    </main>
    <nav class="rivex-tabbar" aria-label="Site footer">
        <ul class="rivex-tabbar__list">
            <li><a class="rivex-tab rivex-tab--active" href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif><svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16 3.2 3.4 14.1c-.9.8-.4 2.3.8 2.3H6V26c0 1.1.9 2 2 2h4.6v-7h6.8v7H24c1.1 0 2-.9 2-2v-9.6h1.8c1.2 0 1.7-1.5.8-2.3z"/></svg><span>Home</span></a></li>
            <li><a class="rivex-tab" href="{{ route('home') }}#offers"><svg viewBox="0 0 32 32" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="24" height="5.5" rx="1"/><path d="M6 16.5V28h20V16.5M16 11v17M16 11c-3 0-6.5-1-6.5-4a2.7 2.7 0 0 1 4.9-1.5C15.3 6.8 16 8.6 16 11zM16 11c3 0 6.5-1 6.5-4a2.7 2.7 0 0 0-4.9-1.5C16.7 6.8 16 8.6 16 11z"/></g></svg><span>Offers</span></a></li>
            <li><button class="rivex-tab" type="button" data-share-url="{{ url()->current() }}"><svg viewBox="0 0 32 32" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" d="M19 5.5 28.5 14 19 22.5v-5.6C11.8 16.9 7.2 19.8 4 26.5c.9-8.6 5.6-14 15-14.8z"/></svg><span>Share</span></button></li>
            <li><a class="rivex-tab" href="{{ auth('web')->check() ? route('account.show') : route('login') }}"><svg viewBox="0 0 32 32" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"><circle cx="16" cy="16" r="13"/><circle cx="16" cy="12.6" r="4.4"/><path d="M7.6 24.6c2-3.4 5-5 8.4-5s6.4 1.6 8.4 5"/></g></svg><span>Profile</span></a></li>
            <li><a class="rivex-tab" href="{{ route('about') }}"><svg viewBox="0 0 32 32" aria-hidden="true"><g fill="currentColor"><circle cx="5.5" cy="16" r="3"/><circle cx="16" cy="16" r="3"/><circle cx="26.5" cy="16" r="3"/></g></svg><span>More</span></a></li>
        </ul>
    </nav>
    <script>document.querySelector('[data-share-url]')?.addEventListener('click',async(event)=>{const url=event.currentTarget.dataset.shareUrl;if(navigator.share)await navigator.share({title:document.title,url}).catch(()=>{});else await navigator.clipboard?.writeText(url)});</script>
</body>
</html>
