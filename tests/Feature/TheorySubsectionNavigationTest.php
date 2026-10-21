<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\Section;

test('theory menu links each admin managed subsection to its own content', function () {
    $section = Section::create(['name' => 'Theory Test Practice']);
    $subSections = collect(range(1, 8))->map(fn ($number) => $section->subSections()->create([
        'name' => 'Admin theory subsection '.$number,
        'icon_path' => $number === 1 ? 'https://example.com/custom-theory.svg' : null,
    ]));
    $otherSection = Section::create(['name' => 'Life in the UK']);
    $otherSection->subSections()->create(['name' => 'Unrelated subsection']);

    foreach ([route('v2.theory'), route('frontend.section', $section)] as $url) {
        $response = $this->get($url)->assertOk()->assertSee('cards--managed', false)
            ->assertSee('https://example.com/custom-theory.svg', false)->assertDontSee('Unrelated subsection')
            ->assertDontSee('href="'.route('v2.guarantee').'"', false);
        foreach ($subSections as $subSection) {
            $response->assertSee($subSection->name)->assertSee('href="'.route('frontend.sub_section', $subSection).'"', false);
        }
    }

    $subSection = $subSections->first();
    Category::create(['sub_section_id' => $subSection->id, 'name_en' => 'Content from this subsection']);
    $this->get(route('frontend.sub_section', $subSection))->assertOk()->assertSee('Content from this subsection');
    $this->get(route('home'))->assertOk()->assertSee(route('v2.theory', ['section' => $section->id]), false);
});

test('theory menu reflects subsection additions renames and deletions', function () {
    $section = Section::create(['name' => 'Theory Test']);
    $subSection = $section->subSections()->create(['name' => 'Original subsection']);
    $url = route('v2.theory', ['section' => $section->id]);
    $this->get($url)->assertOk()->assertSee('Original subsection');

    $subSection->update(['name' => 'Renamed subsection']);
    $added = $section->subSections()->create(['name' => 'New subsection']);
    $this->get($url)->assertOk()->assertSee('Renamed subsection')->assertSee('New subsection')->assertDontSee('Original subsection');

    $subSection->delete();
    $this->get($url)->assertOk()->assertDontSee('Renamed subsection')->assertSee(route('frontend.sub_section', $added), false);

    $section->update(['name' => 'Renamed learning section']);
    $this->get($url)->assertOk()->assertSee('Renamed learning section')->assertSee('New subsection');
});

test('an empty theory menu does not invent subsections and retains direct topics', function () {
    $this->get(route('v2.theory'))->assertOk()->assertSee('No items are available in this section yet.')->assertDontSee('Pass Guarantee');
    $section = Section::create(['name' => 'Theory Test']);
    $topic = $section->topics()->create(['name_en' => 'Direct revision topic']);
    $this->get(route('v2.theory'))->assertOk()->assertSee('Direct revision topic')->assertSee(route('theory.practice', $topic), false)
        ->assertDontSee('No items are available in this section yet.');
});

test('practice revision questions opens the revision page and shows admin managed questions', function () {
    $section = Section::create(['name' => 'Theory Test']);
    $revision = $section->subSections()->create(['name' => 'Practice Revision Question']);
    $topic = $revision->topics()->create(['name_en' => 'Road safety']);
    Question::create([
        'topic_id' => $topic->id,
        'text_en' => 'What should you do before moving off?',
        'question_type' => 'text',
    ]);

    $this->get(route('v2.theory'))
        ->assertOk()
        ->assertSee('Practice Revision Question')
        ->assertSee('href="'.route('v2.revision').'"', false)
        ->assertDontSee('href="'.route('frontend.sub_section', $revision).'"', false);

    $this->get(route('frontend.sub_section', $revision))
        ->assertRedirect(route('v2.revision'));

    $this->get(route('v2.revision'))
        ->assertOk()
        ->assertSee('Road safety')
        ->assertSee('data-topic="'.$topic->id.'" data-total="1"', false);
});
