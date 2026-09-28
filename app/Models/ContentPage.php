<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    public const TYPE_CGI_CLIPS = 'cgi_clips';

    public const TYPE_MOTORWAY_SIGN = 'motorway_sign';

    protected $fillable = [
        'topic_id',
        'admin_title',
        'library_category',
        'type',
        'text_en',
        'text_ku',
        'hazard_window_start',
        'hazard_window_end',
        'hazard_windows',
        'sign_image_path',
        'explanation_en',
        'explanation_ku',
        'what_to_do_en',
        'what_to_do_ku',
        'additional_sign_images',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'hazard_window_start' => 'float',
            'hazard_window_end' => 'float',
            'hazard_windows' => 'array',
            'additional_sign_images' => 'array',
            'translations' => 'array',
        ];
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function clips()
    {
        return $this->hasMany(CgiClip::class)->orderBy('slot');
    }

    public function getSignImagePathAttribute($value)
    {
        return MediaStorage::url($value);
    }

    public function getAdditionalSignImagesAttribute($value): array
    {
        $paths = is_array($value) ? $value : json_decode($value ?: '[]', true);

        return collect($paths ?: [])
            ->map(fn ($path) => MediaStorage::url($path))
            ->filter()
            ->values()
            ->all();
    }
}
