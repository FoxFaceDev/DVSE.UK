<x-layouts.rivex page="section" :title="$section->name" :heading="$section->name" subheading="Choose your next step. Every session counts.">
    @php($fallbackIcons = ['icon-theory.svg', 'icon-hazard.svg', 'icon-highway.svg', 'icon-signs.svg', 'icon-perks.svg'])
    <div class="rivex-stage"><ul class="rivex-cards rivex-cards--section">
        @foreach($section->subSections as $subSection)
            <x-rivex-card :href="route('frontend.sub_section', $subSection)" :name="$subSection->name" :description="$subSection->description" :color="$subSection->color ?? $section->color" :image="$subSection->icon_path" :fallback-image="$fallbackIcons[$loop->index % count($fallbackIcons)]" :compact="true" :index="$loop->index" />
        @endforeach
        @foreach($section->topics as $topic)
            @php($cardIndex = $section->subSections->count() + $loop->index)
            <x-rivex-card :href="$topic->cgi_content_pages_count ? route('theory.hazard_library', $topic) : route('theory.practice', $topic)" :name="$topic->name_en" :color="$section->color" :fallback-image="$fallbackIcons[$cardIndex % count($fallbackIcons)]" :compact="true" :index="$cardIndex" />
        @endforeach
        @if($section->subSections->isEmpty() && $section->topics->isEmpty())<li class="rivex-empty">No items are available in this section yet.</li>@endif
    </ul></div>
</x-layouts.rivex>
