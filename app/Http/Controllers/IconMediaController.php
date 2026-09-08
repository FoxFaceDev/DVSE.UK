<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SubSection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IconMediaController extends Controller
{
    public function __invoke(string $type, int $id): StreamedResponse
    {
        $model = match ($type) {
            'section' => Section::findOrFail($id),
            'sub-section' => SubSection::findOrFail($id),
            default => abort(404),
        };

        $path = ltrim((string) $model->getRawOriginal('icon_path'), '/');
        $path = str_starts_with($path, 'storage/') ? substr($path, 8) : $path;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
