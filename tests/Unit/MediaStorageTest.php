<?php

use App\Models\CgiClip;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config([
        'filesystems.media_disk' => 'r2',
        'filesystems.disks.r2.driver' => 's3',
        'filesystems.disks.r2.url' => 'https://media.hiran.tech',
    ]);

    Storage::fake('r2');
});

test('legacy public paths resolve to the R2 custom domain', function () {
    expect(MediaStorage::url('/storage/questions/example.png'))
        ->toBe('https://media.hiran.tech/questions/example.png');
});

test('R2 URLs are converted back to object keys for deletion', function () {
    Storage::disk('r2')->put('questions/example.png', 'image');

    MediaStorage::delete('https://media.hiran.tech/questions/example.png');

    Storage::disk('r2')->assertMissing('questions/example.png');
});

test('uploaded CGI clips use their direct R2 URL', function () {
    $clip = new CgiClip;
    $clip->setRawAttributes(['media_path' => '/storage/content-pages/cgi/hazard.mp4'], true);

    expect($clip->source)->toBe('https://media.hiran.tech/content-pages/cgi/hazard.mp4');
});

test('external media URLs remain unchanged', function () {
    expect(MediaStorage::url('https://cdn.example.com/video.mp4'))
        ->toBe('https://cdn.example.com/video.mp4');
});
