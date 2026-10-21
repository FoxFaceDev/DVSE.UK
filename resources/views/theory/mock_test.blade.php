<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="color-scheme" content="light">
  <meta name="theme-color" content="#014cc1">
  <title>Questions 2/50 – RIVEX.UK</title>
  <meta name="description" content="Theory test question 2 of 50.">
  <link rel="icon" href="{{ asset('design-v2/Questions_media/img/logo.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('design-v2/Questions_media/fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="{{ asset('design-v2/Questions_media/fonts/open-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('design-v2/Questions_media/css/questions.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/js/app.js')
  <link rel="stylesheet" href="{{ asset('design-v2/ui.css') }}">
</head>
<body x-data="v2Exam({{ Js::from(['questions'=>$questions, 'duration'=>$durationMinutes, 'auto'=>request()->boolean('auto'), 'voice'=>request()->boolean('voice'), 'submit'=>route('theory.mock_test_submit'), 'back'=>isset($mockTest) ? route('theory.dynamic_mock_info', $mockTest) : route('theory.mock_test_info', $subSection)]) }})" :class="current?.media_source ? 'page--media' : 'page--text'" @keydown.escape.window="exitOpen = false">

  <!-- UI glyphs (inline sprite, no scripts) -->
  <svg class="sprite" width="0" height="0" aria-hidden="true" focusable="false">
    <symbol id="i-back" viewBox="0 0 24 24"><path d="M20 12H5M11.5 5.5 5 12l6.5 6.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-audio" viewBox="0 0 24 24"><path fill="currentColor" d="M3 9.3h3.6L11.4 5c.7-.6 1.6-.1 1.6.8v12.4c0 .9-.9 1.4-1.6.8l-4.8-4.3H3a1 1 0 0 1-1-1v-3.4a1 1 0 0 1 1-1z"/><path d="M16 8.8a4.6 4.6 0 0 1 0 6.4M18.9 6a8.6 8.6 0 0 1 0 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 6.8V12h4.2"/></g></symbol>
    <symbol id="i-prev" viewBox="0 0 24 24"><path d="M14.5 5 7.5 12l7 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-next" viewBox="0 0 24 24"><path d="m9.5 5 7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-flag" viewBox="0 0 24 24"><path d="M6 21V4M6 5h11.5l-3 4.2 3 4.3H6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
  </svg>

  <!-- ===== Fixed header: back · "Questions 2/50" · audio, then progress bar + timer (header of theory.html, without the road) ===== -->
  <header class="top">
    <div class="appbar">
      <div class="appbar__inner">
        <div class="appbar__row">
        <a class="back" href="#exit" @click.prevent="exitOpen = true" aria-label="Exit exam">
          <svg class="back__icon" aria-hidden="true"><use href="#i-back"/></svg>
        </a>
        <h1 class="appbar__title">Questions <span class="appbar__count" x-text="(index+1)+'/'+questions.length"></span></h1>
        <button class="back hdr-audio" type="button" @click="speak()" aria-label="Read the question aloud">
          <svg class="back__icon" aria-hidden="true"><use href="#i-audio"/></svg>
        </button>
        </div>
        <div class="appbar__bar">
          <progress class="meter" :max="questions.length || 1" :value="index+1" aria-label="Question progress"></progress>
          <span class="timer" aria-label="Time remaining 56:55"><svg aria-hidden="true"><use href="#i-clock"/></svg><span x-text="time"></span></span>
        </div>
      </div>
    </div>
  </header>

  <!-- ===== Question · (media) · answers ===== -->
  <main class="stage"><template x-if="current"><section class="panel card" aria-labelledby="q-text"><h2 class="q" id="q-text" x-text="current.text_en"></h2><template x-if="current.media_source"><figure class="media"><template x-if="current.media_type !== 'video'"><img class="media__img" :src="current.media_source" :alt="current.text_en"></template><template x-if="current.media_type === 'video' && !youtube(current.media_source)"><video class="media__img" :src="current.media_source" controls playsinline></video></template><template x-if="current.media_type === 'video' && youtube(current.media_source)"><iframe class="media__img" :src="youtube(current.media_source)" title="Question video" allowfullscreen></iframe></template></figure></template><p class="hint" id="q-hint">Select one answer</p><fieldset class="opts" aria-labelledby="q-text" aria-describedby="q-hint" :class="{'v2-image-answers':current.question_type === 'image_answers'}"><template x-for="choice in current.choices" :key="current.id+'-'+choice.id"><label class="opt"><input class="opt__input" type="radio" :name="'answer-'+current.id" :value="choice.id" :checked="answers[current.id] === choice.id" @change="select(choice)"><span class="opt__box" aria-hidden="true"></span><span class="opt__text"><template x-if="choice.image_path"><img class="v2-choice-image" :src="choice.image_path" :alt="choice.text_en || 'Answer image'"></template><span x-text="choice.text_en"></span></span></label></template></fieldset></section></template><div class="v2-empty" x-show="!current">No questions available.</div></main>

  <!-- ===== Fixed footer (as quiz-en.html): Previous · Flag · Next ===== -->
  <footer class="dock">
    <nav class="actions" aria-label="Question actions">
      <a class="act" href="#previous" @click.prevent="previous()" :aria-disabled="index === 0">
        <span class="act__btn"><svg class="act__icon" aria-hidden="true"><use href="#i-prev"/></svg></span>
        <span class="act__label">Previous</span>
      </a>
      <label class="act">
        <input class="act__toggle" type="checkbox" name="flag" aria-label="Flag this question" :checked="isFlagged" @change="toggleFlag()">
        <span class="act__btn"><svg class="act__icon" aria-hidden="true"><use href="#i-flag"/></svg></span>
        <span class="act__label">Flag</span>
      </label>
      <a class="act" href="#next" @click.prevent="next()">
        <span class="act__btn"><svg class="act__icon" aria-hidden="true"><use href="#i-next"/></svg></span>
        <span class="act__label" x-text="index === questions.length-1 ? 'Finish' : 'Next'"></span>
      </a>
    </nav>
  </footer>

<div class="v2-overlay" x-cloak x-show="exitOpen" @click.self="exitOpen=false"><section class="v2-dialog" role="dialog" aria-modal="true" aria-labelledby="exit-title"><h2 id="exit-title">Leave this test?</h2><p>Your current answers will be lost.</p><button class="v2-button" @click="exitOpen=false">Keep going</button><a data-back class="v2-button v2-button--secondary" :href="back">Leave test</a></section></div><div class="v2-error" x-cloak x-show="error" role="alert"><p x-text="error"></p><button class="v2-button" @click="finish()" :disabled="submitting">Retry submission</button></div>
</body>
</html>
