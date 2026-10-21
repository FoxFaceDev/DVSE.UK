<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="color-scheme" content="light">
  <meta name="theme-color" content="#0452bc">
  <title>Practice Revision Questions – RIVEX.UK</title>
  <meta name="description" content="Practise revision questions by category and track your progress.">
  <link rel="icon" href="{{ asset('design-v2/Practice/img/logo.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('design-v2/Practice/fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('design-v2/Practice/css/practice.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/js/app.js')
  <link rel="stylesheet" href="{{ asset('design-v2/ui.css') }}">
  <link rel="stylesheet" href="{{ asset('design-v2/revision.css') }}">
</head>
<body>

  <!-- UI glyphs (inline sprite, no scripts) -->
  <svg class="sprite" width="0" height="0" aria-hidden="true" focusable="false">
    <symbol id="i-back" viewBox="0 0 24 24"><path d="M20 12H5M11.5 5.5 5 12l6.5 6.5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-grid" viewBox="0 0 24 24"><g fill="currentColor"><rect x="3" y="3" width="8" height="8" rx="2.4"/><rect x="13" y="3" width="8" height="8" rx="2.4"/><rect x="3" y="13" width="8" height="8" rx="2.4"/><rect x="13" y="13" width="8" height="8" rx="2.4"/></g></symbol>
    <symbol id="i-bars" viewBox="0 0 24 24"><g fill="currentColor"><rect x="4.2" y="11" width="3.4" height="7.5" rx="1"/><rect x="10.3" y="5" width="3.4" height="13.5" rx="1"/><rect x="16.4" y="8.5" width="3.4" height="10" rx="1"/><rect x="2.5" y="19.8" width="19" height="2" rx="1"/></g></symbol>
    <symbol id="i-bookmark" viewBox="0 0 24 24"><path d="M6.5 3h11a1.5 1.5 0 0 1 1.5 1.5V21l-7-4.6L5 21V4.5A1.5 1.5 0 0 1 6.5 3Z" fill="currentColor"/></symbol>
  </svg>

  <!-- ===== Fixed top: header + tabs ===== -->
  <div class="top">
    <header class="appbar">
      <div class="appbar__inner">
        <a class="back" data-back href="{{ $revisionGroup ? route('frontend.sub_section', $revisionGroup->sub_section_id) : route('v2.theory') }}" aria-label="Back">
          <svg class="back__icon" aria-hidden="true"><use href="#i-back"/></svg>
        </a>
        <h1 class="appbar__title">Practice Revision Questions</h1>
      </div>
    </header>

    <nav class="tabs" aria-label="Practice views">
      <ul class="tabs__list">
        <li class="tabs__item">
          <a class="tab tab--active" href="#category" data-revision-tab="category" aria-current="page">
            <svg class="tab__icon" aria-hidden="true"><use href="#i-grid"/></svg><span>Category</span>
          </a>
        </li>
        <li class="tabs__item">
          <a class="tab" href="#progress" data-revision-tab="progress">
            <svg class="tab__icon" aria-hidden="true"><use href="#i-bars"/></svg><span>Progress</span>
          </a>
        </li>
        <li class="tabs__item">
          <a class="tab" href="#selected" data-revision-tab="selected">
            <svg class="tab__icon" aria-hidden="true"><use href="#i-bookmark"/></svg><span>Selected</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- ===== Categories (6 and a half fit on screen, the rest scroll) ===== -->
  <form id="revision-selection" action="{{ route('v2.revise') }}" method="get">
  @if($revisionGroup)<input type="hidden" name="group" value="{{ $revisionGroup->id }}">@endif
  <main class="stage" id="category">
    <ul class="cats">
@if($categories->isNotEmpty())
<li class="cat" data-topic="all" data-total="{{ $categories->sum('questions_count') }}" data-questions='@json($categories->flatMap(fn ($category) => $category->questions->pluck("id"))->values())'>
<img class="cat__icon" src="{{ asset('design-v2/Practice/img/practice/all-categories.svg') }}" alt="" width="120" height="120">
<div class="cat__body"><div class="cat__head"><h2 class="cat__title"><a class="cat__link" href="{{ route('v2.revise', $revisionGroup ? ['group' => $revisionGroup->id] : []) }}">All categories</a></h2><span class="cat__pct" data-pct>0%</span></div><progress class="cat__bar" max="100" value="0" aria-label="All categories progress"></progress><p class="cat__stats"><span class="cat__stat">Answered: <b data-answered>0</b></span><i class="cat__sep" aria-hidden="true"></i><span class="cat__stat">Correct: <b data-correct>0</b></span><i class="cat__sep" aria-hidden="true"></i><span class="cat__stat">Total: <b>{{ $categories->sum('questions_count') }}</b></span></p></div>
<label class="pick"><input class="pick__input" type="checkbox" data-select-all aria-label="Select All categories"></label></li>
@endif
@foreach($categories as $category)
<li class="cat" style="--revision-color: {{ $category->color ?: '#1a68e8' }}" data-topic="{{ $category->id }}" data-total="{{ $category->questions_count }}" data-questions='@json($category->questions->pluck("id"))'>
<img class="cat__icon" src="{{ $category->icon_path ?: asset('design-v2/Practice/img/practice/all-categories.svg') }}" alt="" width="120" height="120">
<div class="cat__body"><div class="cat__head"><h2 class="cat__title {{ mb_strlen($category->name_en) > 30 ? 'cat__title--long' : '' }}"><a class="cat__link" href="{{ $category->revision_group_id ? route('v2.revise', ['group' => $category->revision_group_id, 'categories' => [$category->id]]) : route('theory.practice', $category) }}">{{ $category->name_en }}</a></h2><span class="cat__pct" data-pct>0%</span></div>
<progress class="cat__bar" max="100" value="0" aria-label="{{ $category->name_en }} progress"></progress>
<p class="cat__stats"><span class="cat__stat">Answered: <b data-answered>0</b></span><i class="cat__sep" aria-hidden="true"></i><span class="cat__stat">Correct: <b data-correct>0</b></span><i class="cat__sep" aria-hidden="true"></i><span class="cat__stat">Total: <b>{{ $category->questions_count }}</b></span></p></div>
<label class="pick"><input class="pick__input" type="checkbox" name="categories[]" value="{{ $category->id }}" aria-label="Select {{ $category->name_en }}"></label></li>
@endforeach
@if($categories->isEmpty())<li class="v2-empty">No revision questions are available yet.</li>@endif</ul>
    <p class="v2-empty revision-empty" data-revision-empty hidden role="status">Select categories in the Category tab to see them here.</p>
  </main>

  <!-- ===== Fixed bottom: hint + continue ===== -->
  <footer class="dock">
    <div class="dock__inner">
      <p class="dock__hint" id="dock-hint">Select one or more categories to revise</p>
      <button class="cta" type="submit" form="revision-selection"><span class="cta__label">Continue</span></button>
    </div>
  </footer>

</form>
</body>
</html>
