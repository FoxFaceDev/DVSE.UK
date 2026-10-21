@props(['category', 'href', 'color' => null])
@php
    $candidateColor = $category->color ?: $color;
    $cardColor = preg_match('/^#[0-9a-fA-F]{6}$/', $candidateColor ?? '') ? $candidateColor : '#1664f5';
    $isQuestions = $category->isQuestionCategory();
    $total = $isQuestions ? ($category->questions_count ?? 0) : ($category->content_pages_count ?? 0);
    $questionIds = $isQuestions && $category->relationLoaded('questions')
        ? $category->questions->pluck('id')->values()
        : collect();
@endphp
<a href="{{ $href }}"
   class="category-card"
   style="--category-color: {{ $cardColor }}"
   @if($isQuestions) data-topic="{{ $category->id }}" data-questions='@json($questionIds)' @endif>
    <span class="category-card__icon-tile">
        <img class="category-card__icon" src="{{ $category->icon_path ?: asset('design-v2/Practice/img/practice/all-categories.svg') }}" alt="" width="72" height="72">
    </span>
    <span class="category-card__body">
        <span class="category-card__head">
            <strong class="category-card__title">{{ $category->name_en }}</strong>
            @if($category->name_ku)<span class="sr-only" lang="ku" dir="rtl">{{ $category->name_ku }}</span>@endif
            <b class="category-card__pct" @if($isQuestions) data-pct @endif>0%</b>
        </span>
        <progress class="category-card__bar" max="100" value="0" aria-label="{{ $category->name_en }} progress">0%</progress>
        <span class="category-card__stats">
            <span>{{ $isQuestions ? 'Answered' : 'Viewed' }}: <b @if($isQuestions) data-answered @endif>0</b></span>
            <i aria-hidden="true"></i>
            <span>{{ $isQuestions ? 'Correct' : 'Complete' }}: <b @if($isQuestions) data-correct @endif>0</b></span>
            <i aria-hidden="true"></i>
            <span>Total: <b>{{ $total }}</b></span>
        </span>
    </span>
    <span class="category-card__pick" aria-hidden="true"></span>
</a>
