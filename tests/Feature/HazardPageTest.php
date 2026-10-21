<?php

use App\Models\Admin;
use App\Models\Section;
use App\Models\Topic;
use App\Support\HazardPage;

test('the hazard entry uses the reference design and reflects admin menu and banner edits', function () {
    $section = Section::where('home_key', 'theory')->firstOrFail();
    $hazard = $section->subSections()->create(['name' => 'Hazard Perception', 'page_type' => 'hazard']);
    $topic = $hazard->topics()->create(['name_en' => 'Town hazards', 'content_type' => Topic::TYPE_LEARNING_PAGES]);
    $topic->contentPages()->create(['type' => 'cgi_clips', 'admin_title' => 'Town clip', 'text_en' => 'Look ahead.']);

    $this->get(route('frontend.sub_section', $hazard))->assertOk()
        ->assertViewIs('v2.hazard-menu')
        ->assertSee('Find_a_Driving_Instructor/css/find-instructor.css', false)
        ->assertSeeInOrder(['Pass Guarantee', 'Help Videos', 'Practise Video Clips', 'Mock Test', 'Progress Monitor', 'Advice and Information']);

    $admin = Admin::create(['name' => 'Hazard editor', 'email' => 'hazard-editor@example.com', 'password' => bcrypt('password')]);
    $this->actingAs($admin, 'admin')->get(route('admin.hazard-page.edit'))->assertOk()->assertSee('Town hazards');

    $settings = HazardPage::settings();
    $settings['title'] = 'Your Hazard Training';
    $settings['banner']['en']['button'] = 'Find your instructor';
    $settings['banner']['ku']['button'] = 'مامۆستا بدۆزەوە';
    $settings['cards']['help']['active'] = false;
    $settings['cards']['clips']['label'] = 'Start town clips';
    $settings['cards']['clips']['destination'] = 'topic:'.$topic->id;
    $settings['cards']['clips']['position'] = 1;
    $settings['cards']['guarantee']['position'] = 2;
    $settings['cards']['clips']['color'] = '#fef3c7';

    $this->actingAs($admin, 'admin')->from(route('admin.hazard-page.edit'))
        ->put(route('admin.hazard-page.update'), $settings)->assertRedirect(route('admin.hazard-page.edit'))->assertSessionHasNoErrors();

    foreach ([route('frontend.sub_section', $hazard), route('v2.hazards')] as $url) {
        $this->get($url)->assertOk()->assertSee('Your Hazard Training')->assertSee('Find your instructor')
            ->assertSeeInOrder(['Start town clips', 'Pass Guarantee'])
            ->assertDontSee('Help Videos')->assertSee('background-color: #fef3c7', false)
            ->assertSee(route('theory.hazard_library', $topic), false);
    }
    $this->get(route('v2.hazards', ['language' => 'ku']))->assertOk()
        ->assertSee('lang="ckb" dir="rtl"', false)->assertSee('مامۆستا بدۆزەوە');

    $hazard->update(['name' => 'Road awareness']);
    $this->get(route('frontend.sub_section', $hazard))->assertOk()->assertViewIs('v2.hazard-menu');
});

test('hazard menu administration is protected and rejects unknown destinations and unsafe colors', function () {
    $this->get(route('admin.hazard-page.edit'))->assertRedirect(route('admin.login'));
    $this->put(route('admin.hazard-page.update'), HazardPage::defaults())->assertRedirect(route('admin.login'));

    $admin = Admin::create(['name' => 'Hazard admin', 'email' => 'hazard-validation@example.com', 'password' => bcrypt('password')]);
    $settings = HazardPage::defaults();
    $settings['cards']['help']['destination'] = 'topic:999999';
    $settings['cards']['help']['color'] = 'red; display:none';
    $this->actingAs($admin, 'admin')->put(route('admin.hazard-page.update'), $settings)
        ->assertSessionHasErrors(['cards.help.destination', 'cards.help.color']);
});

test('the hazard clip library only includes content belonging to the requested subsection', function () {
    $section = Section::where('home_key', 'theory')->firstOrFail();
    $hazard = $section->subSections()->create(['name' => 'Hazard Perception', 'page_type' => 'hazard']);
    $other = $section->subSections()->create(['name' => 'Other area']);
    $direct = $hazard->topics()->create(['name_en' => 'Direct hazards', 'content_type' => Topic::TYPE_LEARNING_PAGES]);
    $group = $hazard->categories()->create(['name_en' => 'Hazard group']);
    $grouped = $group->topics()->create(['name_en' => 'Grouped hazards', 'content_type' => Topic::TYPE_LEARNING_PAGES]);
    $unrelated = $other->topics()->create(['name_en' => 'Unrelated hazards', 'content_type' => Topic::TYPE_LEARNING_PAGES]);
    foreach ([$direct, $grouped, $unrelated] as $topic) {
        $topic->contentPages()->create(['type' => 'cgi_clips', 'admin_title' => 'Clip', 'text_en' => 'Look ahead.']);
    }

    $this->get(route('v2.hazards', ['subsection' => $hazard->id, 'clips' => 1]))
        ->assertOk()->assertSee('Direct hazards')->assertSee('Grouped hazards')->assertDontSee('Unrelated hazards');
    $this->get(route('v2.hazards', ['subsection' => $other->id, 'clips' => 1]))->assertNotFound();
});
