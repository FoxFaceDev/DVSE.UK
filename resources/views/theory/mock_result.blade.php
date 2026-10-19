@php($percentage = $total > 0 ? round($correct / $total * 100) : 0)
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="color-scheme" content="light">
  <meta name="theme-color" content="#014cc1">
  <title>Mock Test Results – RIVEX.UK</title>
  <meta name="description" content="Your mock theory test results: score, best and worst category and time taken.">
  <link rel="icon" href="{{ asset('design-v2/Mock_test_result/img/logo.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('design-v2/Mock_test_result/fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('design-v2/Mock_test_result/css/result.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/js/app.js')
  <link rel="stylesheet" href="{{ asset('design-v2/ui.css') }}">
</head>
<body x-data="{ showReview: false }">

  <!-- UI glyphs (inline sprite, no scripts) -->
  <svg class="sprite" width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
      <linearGradient id="ring-grad" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#3b8bff"/><stop offset="1" stop-color="#0b55dd"/></linearGradient>
    </defs>
    <symbol id="i-back" viewBox="0 0 24 24"><path d="M20 12H5M11.5 5.5 5 12l6.5 6.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-bars" viewBox="0 0 24 24"><g fill="currentColor"><rect x="3.5" y="12" width="4.6" height="8.5" rx="1.6"/><rect x="9.7" y="7.5" width="4.6" height="13" rx="1.6"/><rect x="15.9" y="3.5" width="4.6" height="17" rx="1.6"/></g></symbol>
    <symbol id="i-doc" viewBox="0 0 24 24"><path fill="currentColor" d="M6.5 2h8.3L20 7.2V20a2 2 0 0 1-2 2H6.5a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm7.5 1.8V8h4.2zM8 11.2a.9.9 0 0 0 0 1.8h8a.9.9 0 0 0 0-1.8zm0 3.6a.9.9 0 0 0 0 1.8h8a.9.9 0 0 0 0-1.8zM8 7.6a.9.9 0 0 0 0 1.8h3.5a.9.9 0 0 0 0-1.8z"/></symbol>
    <symbol id="i-trophy" viewBox="0 0 24 24"><path fill="currentColor" d="M7 2.5h10a1 1 0 0 1 1 1V4h2.6a1 1 0 0 1 1 1.1c-.3 3.3-2 5.5-4.5 6.1A6 6 0 0 1 13 14.4V17h2.5a1.5 1.5 0 0 1 1.5 1.5V21H7v-2.5A1.5 1.5 0 0 1 8.5 17H11v-2.6a6 6 0 0 1-4.1-3.2C4.4 10.6 2.7 8.4 2.4 5.1A1 1 0 0 1 3.4 4H6v-.5a1 1 0 0 1 1-1zM4.5 6c.4 1.8 1.3 3 2.4 3.6A10 10 0 0 1 6.2 6H4.5zm15 0h-1.7a10 10 0 0 1-.7 3.6c1.1-.6 2-1.8 2.4-3.6z"/></symbol>
    <symbol id="i-warn" viewBox="0 0 24 24"><path fill="currentColor" d="M10.3 3.1a2 2 0 0 1 3.4 0l8.6 15a2 2 0 0 1-1.7 3H3.4a2 2 0 0 1-1.7-3z"/><path d="M12 8.6v5.6" stroke="#fff" stroke-width="2.4" stroke-linecap="round"/><circle cx="12" cy="17.4" r="1.4" fill="#fff"/></symbol>
    <symbol id="i-up" viewBox="0 0 24 24"><path d="M3 17.5 9.5 11l4 4L21 7.5M15 7.5h6v6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-down" viewBox="0 0 24 24"><path d="M3 6.5 9.5 13l4-4 7.5 7.5M15 16.5h6v-6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 6.8V12h4.2"/></g></symbol>
    <symbol id="i-hourglass" viewBox="0 0 24 24"><path fill="currentColor" d="M6 2h12a1 1 0 0 1 0 2h-.6v2.3a5 5 0 0 1-2.2 4.1L13.7 12l1.5 1.6a5 5 0 0 1 2.2 4.1V20h.6a1 1 0 0 1 0 2H6a1 1 0 0 1 0-2h.6v-2.3a5 5 0 0 1 2.2-4.1l1.5-1.6-1.5-1.6a5 5 0 0 1-2.2-4.1V4H6a1 1 0 0 1 0-2zm2.6 2v2.3c0 .6.2 1.2.6 1.7h5.6c.4-.5.6-1.1.6-1.7V4zM12 14.3l-2 2a3 3 0 0 0-1.2 1.6h6.4a3 3 0 0 0-1.2-1.6z"/></symbol>
    <symbol id="i-review" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2.8H6.8A1.8 1.8 0 0 0 5 4.6v14.8a1.8 1.8 0 0 0 1.8 1.8h10.4a1.8 1.8 0 0 0 1.8-1.8V7.3z"/><path d="M8.5 9.5h7M8.5 13h7M8.5 16.5h4"/></g></symbol>
    <symbol id="i-retry" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M19.5 9A8 8 0 0 0 5.1 7.2L4 8.5M4.5 15a8 8 0 0 0 14.4 1.8L20 15.5"/><path d="M4 3.8v4.7h4.7M20 20.2v-4.7h-4.7"/></g></symbol>
  </svg>

  <!-- ===== Fixed header: back · title · road art (same as theory.html) ===== -->
  <header class="top">
    <div class="appbar">
      <div class="appbar__inner">
        <span class="appbar__art" aria-hidden="true"></span>
        <div class="appbar__row">
        <a class="back" href="{{ route('v2.mock') }}" aria-label="Back">
          <svg class="back__icon" aria-hidden="true"><use href="#i-back"/></svg>
        </a>
        <h1 class="appbar__title">Mock Test Results</h1>
        </div>
      </div>
    </div>
  </header>

  <main class="results">
    @include('v2.site-ad')

    <!-- Score -->
    <section class="panel score" aria-label="Your score">
      <div class="ring" role="img" aria-label="{{ $percentage }}% correct answers">
        <svg class="ring__svg" viewBox="0 0 100 100" aria-hidden="true">
          <circle class="ring__track" cx="50" cy="50" r="42"/>
          <circle class="ring__bar" cx="50" cy="50" r="42" pathLength="100" stroke-dasharray="{{ $percentage }} 100"/>
        </svg>
        <span class="ring__text" aria-hidden="true"><span class="ring__pct">{{ $percentage }}%</span><span class="ring__cap">Correct<br>answers</span></span>
      </div>
      <div class="score__head">
        <div class="score__top">
          <span class="ico ico--blue" aria-hidden="true"><svg><use href="#i-bars"/></svg></span>
          <p class="score__label">Your score</p>
        </div>
        <p class="score__value"><b>{{ $correct }}</b> / {{ $total }}</p>
        <p class="score__pass">The pass mark is {{ $passMark }}/{{ $total }}</p>
      </div>
    </section>

    <!-- 1 · Best category (full width) -->
    <section class="cat cat--best" aria-label="Best category">
      <span class="ico ico--green" aria-hidden="true"><svg><use href="#i-trophy"/></svg></span>
      <p class="cat__body"><span class="cat__label">Best category,</span>   <span class="cat__value">{{ $bestCategory ?? "—" }}</span></p>
      <svg class="cat__trend" aria-hidden="true"><use href="#i-up"/></svg>
    </section>

    <!-- 2 · Worst category (full width, under the best one) -->
    <section class="cat cat--worst" aria-label="Worst category">
      <span class="ico ico--red" aria-hidden="true"><svg><use href="#i-warn"/></svg></span>
      <p class="cat__body"><span class="cat__label">Worst category,</span>   <span class="cat__value">{{ $worstCategory ?? "—" }}</span></p>
      <svg class="cat__trend" aria-hidden="true"><use href="#i-down"/></svg>
    </section>

    <!-- Time -->
    <section class="panel time" aria-label="Time">
      <div class="time__item">
        <span class="ico ico--blue" aria-hidden="true"><svg><use href="#i-clock"/></svg></span>
        <span><span class="time__label">Time taken</span><span class="time__value">{{ $timeTaken ?? "—" }}</span></span>
      </div>
      <span class="time__sep" aria-hidden="true"></span>
      <div class="time__item">
        <span class="ico ico--gold" aria-hidden="true"><svg><use href="#i-hourglass"/></svg></span>
        <span><span class="time__label">Time remaining</span><span class="time__value">{{ $timeRemaining ?? "—" }}</span></span>
      </div>
    </section>

    <!-- Encouragement -->
    <section class="cheer" aria-labelledby="cheer-title">
      <img class="cheer__img" src="{{ asset('design-v2/Mock_test_result/img/mock/result-girl.webp') }}" alt="" width="792" height="700">
      <div class="cheer__body">
        <h2 class="cheer__title" id="cheer-title">{{ $passed ? "Well done!" : "Keep improving!" }}</h2>
        <p class="cheer__text">Review your answers and build your knowledge for next time.</p>
      </div>
    </section>
  <section id="review" class="v2-review" x-cloak x-show="showReview">
    <h2>Review your answers</h2>
    @forelse($reviews as $review)
      <article class="v2-review-card">
        <h3>{{ $review['question'] }}</h3>
        @if(($review['question_image'] ?? null) && ($review['media_type'] ?? null) !== 'video')<img src="{{ $review['question_image'] }}" alt="Question image">@endif
        <p>Your answer: {{ $review['selected'] ?? 'Not answered' }}</p>
        <p class="v2-correct">Correct answer: {{ $review['correct'] ?? '' }}</p>
        @foreach($review['choices'] ?? [] as $choice)
          <div class="v2-review-choice {{ $choice['is_correct'] ? 'v2-review-choice--correct' : ($choice['is_selected'] ? 'v2-review-choice--wrong' : '') }}">
            @if($choice['image'] ?? null)<img src="{{ $choice['image'] }}" alt="{{ $choice['text'] ?: 'Answer image' }}">@endif
            <span>{{ $choice['text'] }}</span>
            @if($choice['is_correct'])<strong>Correct</strong>@elseif($choice['is_selected'])<strong>Your answer</strong>@endif
          </div>
        @endforeach
        <p>{{ $review['explanation'] }}</p>
      </article>
    @empty
      <p>No answer review is available for this test.</p>
    @endforelse
  </section></main>

  <!-- ===== Fixed dock: actions ===== -->
  <nav class="dock" aria-label="Next steps">
    <div class="actions">
      <a class="btn btn--primary" href="#review" @click.prevent="showReview=true; $nextTick(() => document.getElementById('review').scrollIntoView({behavior:'smooth'}))">
        <svg class="btn__icon" aria-hidden="true"><use href="#i-review"/></svg>
        <span>Review your answers</span>
      </a>
      <a class="btn btn--ghost" href="{{ route('v2.mock') }}">
        <svg class="btn__icon" aria-hidden="true"><use href="#i-retry"/></svg>
        <span>Start a new mock test</span>
      </a>
    </div>
  </nav>

@include('v2.site-extras')
</body>
</html>
