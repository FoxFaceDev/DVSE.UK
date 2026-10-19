<x-layouts.app title="Search Questions" :backUrl="route('v2.theory')">
    <form class="v2-search" method="get" action="{{ route('v2.search') }}" role="search"><label class="sr-only" for="question-search">Search questions</label><input id="question-search" type="search" name="q" value="{{ $term }}" placeholder="Search revision questions…" maxlength="200"><button class="v2-button">Search</button></form>
    @forelse($questions as $question)<a class="v2-question-link" href="{{ route('v2.revise', ['question'=>$question->id]) }}">{{ $question->text_en }}<small>{{ $question->topic?->name_en }}</small></a>@empty<div class="v2-empty">No questions match your search.</div>@endforelse
    {{ $questions->links() }}
</x-layouts.app>
