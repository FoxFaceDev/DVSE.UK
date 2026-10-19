<x-layouts.app title="More">
    <section class="v2-panel">
        @foreach($sections as $section)<a class="v2-link" href="{{ route('frontend.section', $section) }}" style="--learning-color: {{ $section->color }}">{{ $section->name }}</a>@endforeach
        <a class="v2-link" href="{{ route('learn') }}">All learning sections</a><a class="v2-link" href="{{ route('v2.theory') }}">Theory Test</a><a class="v2-link" href="{{ route('v2.hazards') }}">Hazard Perception</a><a class="v2-link" href="{{ route('instructors.index') }}">Find an Instructor</a><a class="v2-link" href="{{ route('v2.progress') }}">Learning Progress</a><a class="v2-link" href="{{ route('about') }}">About us</a><a class="v2-link" href="{{ route('contact') }}">Contact us</a>
        @auth('web')<a class="v2-link" href="{{ route('account.show') }}">Account settings</a>@if(auth('web')->user()->isInstructor())<a class="v2-link" href="{{ route('instructors.edit') }}">Manage instructor profile</a>@endif<form action="{{ route('logout') }}" method="post">@csrf<button class="v2-button">Sign out</button></form>@else<a class="v2-link" href="{{ route('login') }}">Sign in</a><a class="v2-link" href="{{ route('register') }}">Create an account</a>@endauth
    </section>
</x-layouts.app>
