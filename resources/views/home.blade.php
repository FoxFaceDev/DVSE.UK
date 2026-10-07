<x-layouts.rivex page="home" title="Home">
    @php($fallbackIcons = ['icon-theory.webp', 'icon-find.webp', 'icon-join.webp', 'icon-become.webp'])
    <div class="rivex-stage"><ul class="rivex-cards rivex-cards--home">
        @forelse($sections as $section)
            <x-rivex-card :href="route('frontend.section', $section)" :name="$section->name" :description="$section->description" :color="$section->color" :image="$section->icon_path" :fallback-image="$fallbackIcons[$loop->index % count($fallbackIcons)]" :index="$loop->index" />
        @empty
            <li class="rivex-empty">Your learning journey starts here. New sections are on their way.</li>
        @endforelse
    </ul></div>
</x-layouts.rivex>
