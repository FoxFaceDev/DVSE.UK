<?php

namespace App\Http\Controllers;

use App\Models\CgiClip;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CgiClipMediaController extends Controller
{
    public function __invoke(CgiClip $cgiClip): BinaryFileResponse|RedirectResponse
    {
        $storedPath = $cgiClip->getRawOriginal('media_path');
        abort_unless($storedPath, 404);

        $relativePath = MediaStorage::key($storedPath);
        abort_unless($relativePath, 404);

        if (! MediaStorage::isLocal()) {
            return redirect()->away(MediaStorage::url($storedPath));
        }

        $absolutePath = Storage::disk(MediaStorage::diskName())->path($relativePath);
        abort_unless(is_file($absolutePath), 404);

        return response()->file($absolutePath, [
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
