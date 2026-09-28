<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Model;

class CgiClip extends Model
{
    protected $fillable = [
        'content_page_id',
        'slot',
        'media_path',
        'media_url',
        'thumbnail_path',
    ];

    protected $appends = ['source'];

    public function contentPage()
    {
        return $this->belongsTo(ContentPage::class);
    }

    public function getMediaPathAttribute($value)
    {
        return MediaStorage::url($value);
    }

    public function getSourceAttribute()
    {
        if ($this->getRawOriginal('media_path')) {
            return MediaStorage::isLocal()
                ? route('media.cgi-clips.stream', $this, false)
                : MediaStorage::url($this->getRawOriginal('media_path'));
        }

        return $this->media_url;
    }

    public function getThumbnailPathAttribute($value)
    {
        return MediaStorage::url($value);
    }
}
