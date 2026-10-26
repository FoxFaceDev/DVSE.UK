<li>
  <a class="card" href="{{ $mockTest->type === 'hazard' ? route('theory.hazard_mock_info') : route('theory.dynamic_mock_info', $mockTest) }}" style="--admin-card-color: {{ $subSection->color ?: $subSection->section->color ?: '#1a5fe0' }}">
    @include('v2._theory-card-wave')
    <img class="card__icon" src="{{ asset('design-v2/Theory/img/theory/mock.svg') }}" alt="" width="120" height="100">
    <span class="card__title">{{ $mockTest->name }}</span>
    <span class="sr-only">{{ $mockTest->type === 'hazard' ? 'Hazard perception' : 'English-only theory test' }} · {{ $mockTest->description ?: $mockTest->question_count.' questions · '.$mockTest->duration_minutes.' minutes' }}</span>
  </a>
</li>
