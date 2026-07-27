<?php

namespace App\Http\Controllers;

use App\Models\CgiClip;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CgiClipMediaController extends Controller
{
    public function __invoke(CgiClip $cgiClip): BinaryFileResponse
    {
        $storedPath = $cgiClip->getRawOriginal('media_path');
        abort_unless($storedPath, 404);

        $relativePath = ltrim(preg_replace('#^/?storage/#', '', $storedPath), '/');
        abort_if($relativePath === '' || str_contains($relativePath, '..'), 404);

        $absolutePath = Storage::disk('public')->path($relativePath);
        abort_unless(is_file($absolutePath), 404);

        return response()->file($absolutePath, [
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
