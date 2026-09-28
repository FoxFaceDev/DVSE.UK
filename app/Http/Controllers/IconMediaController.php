<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SubSection;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IconMediaController extends Controller
{
    public function __invoke(string $type, int $id): StreamedResponse|RedirectResponse
    {
        $model = match ($type) {
            'section' => Section::findOrFail($id),
            'sub-section' => SubSection::findOrFail($id),
            default => abort(404),
        };

        $storedPath = $model->getRawOriginal('icon_path');
        $path = MediaStorage::key($storedPath);

        abort_unless($path, 404);

        if (! MediaStorage::isLocal()) {
            return redirect()->away(MediaStorage::url($storedPath));
        }

        abort_unless(Storage::disk(MediaStorage::diskName())->exists($path), 404);

        return Storage::disk(MediaStorage::diskName())->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
