<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'topic_id', 'question_type', 'text_en', 'text_ku', 'translations', 'media_path',
        'media_type', 'media_url',
        'explanation_en', 'explanation_ku'
    ];

    protected $casts = ['translations' => 'array'];

    protected $appends = ['media_source'];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function getMediaPathAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        
        // Ensure the path starts with /storage/ for the browser to find it from the root
        $path = str_replace('/storage/', '', $value);
        return '/storage/' . ltrim($path, '/');
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
        if (!$this->is_youtube) return null;
        preg_match('/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/', $this->media_url, $match);
        return (isset($match[2]) && strlen($match[2]) === 11) ? $match[2] : null;
    }

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
}
