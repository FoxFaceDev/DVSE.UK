<?php

use App\Models\Category;
use App\Models\ContentPage;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Topic;
use Illuminate\Support\Facades\Storage;

function playbackTopic(): Topic
{
    $section = Section::create(['name' => 'Theory']);
    $subSection = SubSection::create([
        'section_id' => $section->id,
        'name' => 'Hazard perception',
    ]);
    $category = Category::create([
        'sub_section_id' => $subSection->id,
        'name_en' => 'Developing hazards',
    ]);

    return Topic::create([
        'topicable_type' => Category::class,
        'topicable_id' => $category->id,
        'name_en' => 'Developing hazards',
    ]);
}

test('uploaded CGI clips are served with byte range support for seeking', function () {
    Storage::fake('public');
    Storage::disk('public')->put('content-pages/cgi/explanation.mp4', '0123456789');

    $page = ContentPage::create([
        'topic_id' => playbackTopic()->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
    ]);
    $clip = $page->clips()->create([
        'slot' => 1,
        'media_path' => '/storage/content-pages/cgi/explanation.mp4',
    ]);

    expect($clip->source)->toBe(route('media.cgi-clips.stream', $clip, false));

    $this->withHeader('Range', 'bytes=2-5')
        ->get(route('media.cgi-clips.stream', $clip))
        ->assertStatus(206)
        ->assertHeader('Accept-Ranges', 'bytes')
        ->assertHeader('Content-Length', 4)
        ->assertHeader('Content-Range', 'bytes 2-5/10');
});

test('remote CGI clip URLs remain unchanged', function () {
    $page = ContentPage::create([
        'topic_id' => playbackTopic()->id,
        'type' => ContentPage::TYPE_CGI_CLIPS,
    ]);
    $clip = $page->clips()->create([
        'slot' => 1,
        'media_url' => 'https://cdn.example.com/explanation.mp4',
    ]);

    expect($clip->source)->toBe('https://cdn.example.com/explanation.mp4');
});
