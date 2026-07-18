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
        'sign_image_path',
        'explanation_en',
        'explanation_ku',
    ];

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
