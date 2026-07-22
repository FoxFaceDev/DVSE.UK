<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    public const TYPE_CGI_CLIPS = 'cgi_clips';

    public const TYPE_MOTORWAY_SIGN = 'motorway_sign';

    protected $fillable = [
        'category_id',
        'type',
        'text_en',
        'text_ku',
        'hazard_window_start',
        'hazard_window_end',
        'hazard_windows',
        'sign_image_path',
        'explanation_en',
        'explanation_ku',
    ];

    protected function casts(): array
    {
        return [
            'hazard_window_start' => 'float',
            'hazard_window_end' => 'float',
            'hazard_windows' => 'array',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function clips()
    {
        return $this->hasMany(CgiClip::class)->orderBy('slot');
    }

    public function getSignImagePathAttribute($value)
    {
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        return '/storage/'.ltrim(str_replace('/storage/', '', $value), '/');
    }
}
