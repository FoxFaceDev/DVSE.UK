@props(['mockTest' => null, 'fallbackHazard' => false])

@if($mockTest)
    <a href="{{ $mockTest->type === 'hazard' ? route('theory.hazard_mock_info') : route('theory.dynamic_mock_info', $mockTest) }}"
       class="{{ $mockTest->type === 'hazard' ? 'tone-coral' : 'tone-green' }} test-choice group block">
        <span class="eyebrow">{{ $mockTest->type === 'hazard' ? 'Hazard perception' : 'English-only theory test' }}</span>
        <h2>{{ $mockTest->name }}</h2>
        <p>{{ $mockTest->description ?: ($mockTest->type === 'theory' ? $mockTest->question_count.' questions · '.$mockTest->duration_minutes.' minutes' : 'Official-style hazard clips') }}</p>
        <span class="test-choice-link">Start mock test <x-study-icon type="arrow" /></span>
    </a>
@elseif($fallbackHazard)
    <a href="{{ route('theory.hazard_mock_info') }}" class="tone-coral test-choice group block">
        <span class="eyebrow">Official Mock Test · Hazard perception</span>
        <h2>Hazard Mock Test</h2>
        <p>14 clips · Approximately 15 minutes</p>
        <span class="test-choice-link">Start mock test <x-study-icon type="arrow" /></span>
    </a>
@endif
