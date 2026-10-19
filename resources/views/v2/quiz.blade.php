<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="color-scheme" content="light">
  <meta name="theme-color" content="#0452bc">
  <title>Practice Question – RIVEX.UK</title>
  <meta name="description" content="Practice question in English.">
  <link rel="icon" href="{{ asset('design-v2/Quiz/img/logo.png') }}" type="image/png">
  <link rel="preload" href="{{ asset('design-v2/Quiz/fonts/open-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('design-v2/Quiz/css/quiz.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/js/app.js')
  <link rel="stylesheet" href="{{ asset('design-v2/ui.css') }}">
</head>
<body x-data="v2Quiz({{ Js::from(['questions'=>$questions, 'languages'=>$languages, 'language'=>request('language', $preferredLanguage?->code ?? 'en'), 'flagged'=>$flagged ?? false, 'questionId'=>request('question'), 'result'=>route('theory.result'), 'ads'=>$ads ?? []]) }})" :class="language === 'en' ? 'mode-en' : 'mode-ku'" @keydown.escape.window="if(modal !== 'ad' || adCountdown <= 0) modal = null">

  <svg class="sprite" width="0" height="0" aria-hidden="true" focusable="false">
    <symbol id="i-back" viewBox="0 0 24 24"><path d="M20 12H5M11.5 5.5 5 12l6.5 6.5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-swap" viewBox="0 0 24 24"><path d="M7.5 4 3.5 8l4 4M3.5 8H18M16.5 20l4-4-4-4M20.5 16H6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-audio" viewBox="0 0 24 24"><path d="M3.5 9.6h3.4L12 5.4v13.2l-5.1-4.2H3.5z" fill="currentColor"/><path d="M15.4 9.2a4 4 0 0 1 0 5.6M18.2 6.6a8 8 0 0 1 0 10.8" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/></symbol>
    <symbol id="i-prev" viewBox="0 0 24 24"><path d="M14.5 5 7.5 12l7 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-next" viewBox="0 0 24 24"><path d="m9.5 5 7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-flag" viewBox="0 0 24 24"><path d="M6 21V4M6 5h11.5l-3 4.2 3 4.3H6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-pencil" viewBox="0 0 24 24"><path d="M4 20.2 5 16 16.6 4.4a2.1 2.1 0 0 1 3 0 2.1 2.1 0 0 1 0 3L8 19zM14.4 6.6l3 3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-bulb" viewBox="0 0 24 24"><path d="M9.2 18.2h5.6M10.2 21h3.6M12 3a6 6 0 0 0-3.6 10.8c.7.5 1.1 1.2 1.1 2v.4h5v-.4c0-.8.4-1.5 1.1-2A6 6 0 0 0 12 3Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="m5.5 12.8 4.3 4.3 8.7-9.2" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-cross" viewBox="0 0 24 24"><path d="m6.8 6.8 10.4 10.4M17.2 6.8 6.8 17.2" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></symbol>
    <symbol id="i-spark" viewBox="0 0 24 24"><path d="M10.5 3.5 12.3 9l5.5 1.8-5.5 1.8-1.8 5.5-1.8-5.5-5.5-1.8L8.7 9zM18.6 14.2l.9 2.3 2.3.9-2.3.9-.9 2.3-.9-2.3-2.3-.9 2.3-.9z" fill="currentColor"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 2.8 19.4 6v5.6c0 4.5-3 8.2-7.4 9.6-4.4-1.4-7.4-5.1-7.4-9.6V6z" fill="currentColor"/><path d="m8.8 12.2 2.3 2.3 4.2-4.6" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
  </svg>

  <!-- ===== Fixed top: back + tools, question counter, progress ===== -->
  <header class="top">
    <div class="appbar">
      <div class="appbar__inner">
        <div class="appbar__row">
          <a class="back" href="{{ route('v2.revision') }}" aria-label="Back to categories">
            <svg class="back__icon" aria-hidden="true"><use href="#i-back"/></svg>
          </a>
          <div class="tools">
            <a class="tool" href="#language" @click.prevent="modal = 'language'" title="English + Kurdish">
              <span class="tool__btn"><svg class="tool__icon" aria-hidden="true"><use href="#i-swap"/></svg></span>
              <span class="tool__label">Language</span>
            </a>
            <button class="tool" type="button" @click="speak()" aria-label="Read question aloud">
              <span class="tool__btn"><svg class="tool__icon" aria-hidden="true"><use href="#i-audio"/></svg></span>
              <span class="tool__label">Audio</span>
            </button>
          </div>
        </div>
        <h1 class="appbar__title" x-text="questions.length ? `Question ${index + 1}/${questions.length}` : 'Revision'"></h1>
        <div class="appbar__bar">
          <progress class="meter" :max="questions.length || 1" :value="index + 1" aria-label="Question progress"></progress>
        </div>
      </div>
    </div>
  </header>

  <!-- ===== Question ===== -->
  <template x-if="current"><main class="quiz" id="question" :class="{'quiz--media': current.media_source}">
<div class="quiz__head"><template x-if="current.media_source"><figure class="media"><template x-if="current.media_type !== 'video'"><img class="media__img" :src="current.media_source" :alt="current.text_en"></template><template x-if="current.media_type === 'video' && !youtube(current.media_source)"><video :src="current.media_source" controls playsinline class="media__img"></video></template><template x-if="current.media_type === 'video' && youtube(current.media_source)"><iframe :src="youtube(current.media_source)" title="Question video" allowfullscreen class="media__img"></iframe></template></figure></template><h2 class="q"><span class="q__en" x-text="current.text_en"></span><span class="q__ku ku" x-show="language !== 'en'" :dir="direction" x-text="translated(current, 'text')"></span></h2></div>
<div class="quiz__answers"><p class="meta"><span class="meta__hint">Select one answer</span><span class="meta__src">Revision question</span></p><fieldset class="opts" :class="{'is-answered': answered, 'v2-image-answers':current.question_type === 'image_answers'}"><legend class="sr-only">Choose one answer</legend>
<template x-for="choice in current.choices" :key="current.id + '-' + choice.id"><label class="opt" :class="{'opt--correct': answered && choice.is_correct, 'is-picked': answer === choice.id}"><input class="opt__input" type="radio" :name="'answer-'+current.id" :value="choice.id" :checked="answer === choice.id" :disabled="answered" @change="select(choice)"><span class="opt__text"><template x-if="choice.image_path"><img class="v2-choice-image" :src="choice.image_path" :alt="choice.text_en || 'Answer image'"></template><span class="opt__en" x-text="choice.text_en"></span><span class="opt__ku ku" x-show="language !== 'en'" :dir="direction" x-text="translated(choice,'text')"></span></span><span class="opt__mark" aria-hidden="true"><svg class="mark mark--ok"><use href="#i-check"/></svg><svg class="mark mark--no"><use href="#i-cross"/></svg></span></label></template></fieldset></div></main></template>
<div class="v2-empty" x-cloak x-show="!current">No questions available<span x-show="flaggedOnly">. Flag questions while revising to find them here</span>.</div>

  <!-- ===== Fixed bottom: actions ===== -->
  <footer class="dock">
    <nav class="actions" aria-label="Question actions">
      <a class="act" href="#previous" @click.prevent="previous()" :aria-disabled="index === 0">
        <span class="act__btn"><svg class="act__icon" aria-hidden="true"><use href="#i-prev"/></svg></span>
        <span class="act__label">Previous</span>
      </a>
      <label class="act">
        <input class="act__toggle" type="checkbox" aria-label="Flag this question" :checked="isFlagged" @change="toggleFlag()">
        <span class="act__btn"><svg class="act__icon" aria-hidden="true"><use href="#i-flag"/></svg></span>
        <span class="act__label">Flag</span>
      </label>
      <button class="act" type="button" @click="modal = 'word'">
        <span class="act__btn"><svg class="act__icon" aria-hidden="true"><use href="#i-pencil"/></svg></span>
        <span class="act__label">AI Word</span>
      </button>
      <button class="act" type="button" @click="modal = 'explain'">
        <span class="act__btn"><svg class="act__icon" aria-hidden="true"><use href="#i-bulb"/></svg></span>
        <span class="act__label">Explain</span>
      </button>
      <a class="act" href="#next" @click.prevent="next()">
        <span class="act__btn"><svg class="act__icon" aria-hidden="true"><use href="#i-next"/></svg></span>
        <span class="act__label" x-text="index === questions.length-1 ? 'Finish' : 'Next'"></span>
      </a>
    </nav>
  </footer>

@include('v2.quiz-modal')
@include('v2.site-extras')
</body>
</html>
