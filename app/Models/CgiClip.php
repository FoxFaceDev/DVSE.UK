<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CgiClip extends Model
{
    protected $fillable = [
        'content_page_id',
        'slot',
        'media_path',
        'media_url',
    ];

    protected $appends = ['source'];

    public function contentPage()
    {
        return $this->belongsTo(ContentPage::class);
    }

    public function getMediaPathAttribute($value)
    {
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        return '/storage/'.ltrim(str_replace('/storage/', '', $value), '/');
    }

    public function getSourceAttribute()
    {
        return $this->media_path ?? $this->media_url;
    }
}
