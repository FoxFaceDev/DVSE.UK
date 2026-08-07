<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'language_id', 'title', 'media_type', 'media_path', 'media_url',
        'link_url', 'targets_all_categories', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'targets_all_categories' => 'boolean',
    ];

    protected $appends = ['media_source'];

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function getMediaPathAttribute($value)
    {
        if (! $value) {
            return null;
        }
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        $path = str_replace('/storage/', '', $value);

        return '/storage/'.ltrim($path, '/');
    }

    /**
     * Get the effective media source (prefers uploaded file over URL).
     */
    public function getMediaSourceAttribute()
    {
        return $this->media_path ?? $this->media_url;
    }

    public function getIsYoutubeAttribute()
    {
        return $this->media_url && (str_contains($this->media_url, 'youtube.com') || str_contains($this->media_url, 'youtu.be'));
    }

    public function getYoutubeIdAttribute()
    {
        if (! $this->is_youtube) {
            return null;
        }
        preg_match('/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/', $this->media_url, $match);

        return (isset($match[2]) && strlen($match[2]) === 11) ? $match[2] : null;
    }
}
