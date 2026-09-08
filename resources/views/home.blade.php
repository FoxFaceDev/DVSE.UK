<x-layouts.app :showBack="false">
<div class="home-welcome"><div><p class="eyebrow">YOUR DRIVING JOURNEY</p><h1>Small steps.<br><span>Safer miles.</span></h1></div><div class="welcome-symbol" aria-hidden="true"><svg viewBox="0 0 80 80" fill="none"><path d="M16 72 31 8h18l15 64" stroke="currentColor" stroke-width="3"/><path d="M40 13v9m0 9v10m0 10v13" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><circle cx="59" cy="22" r="14" fill="#ddf5bd"/><path d="m53 22 4 4 8-9" stroke="#31572d" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div></div>
<p class="welcome-description">Build your confidence, one practice at a time.</p>
<div class="section-heading" id="learning"><h2>Let’s get you road-ready</h2><span>LEARN · PRACTISE · PASS</span></div>
<div class="mode-grid">
@forelse($sections as $section)
<x-mode-card :href="route('frontend.section', $section->id)" :name="$section->name" :description="$section->description" :color="$section->color" :image="$section->icon_path" :index="$loop->index" />
@empty
<div class="empty-state">Your learning journey starts here. New sections are on their way.</div>
@endforelse
</div>
<div class="journey-note"><span class="note-icon"><x-study-icon type="check" /></span><div><strong>A little progress, every day.</strong><p>Make time to practise. Your future self will thank you.</p></div></div>
</x-layouts.app>
