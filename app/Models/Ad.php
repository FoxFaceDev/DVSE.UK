<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'language_id', 'title', 'display_type', 'placements', 'media_type', 'media_path', 'media_url',
        'link_url', 'advertiser_email', 'starts_at', 'expires_at', 'activation_notified_at',
        'expiry_warning_notified_at', 'targets_all_categories', 'targets_all_topics', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'targets_all_categories' => 'boolean',
        'targets_all_topics' => 'boolean',
        'placements' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'activation_notified_at' => 'datetime',
        'expiry_warning_notified_at' => 'datetime',
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

    public function languages()
    {
        return $this->belongsToMany(Language::class)->withTimestamps();
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class)->withTimestamps();
    }

    public function scopeCurrentlyRunning($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()));
    }

    public function scopeForLanguage($query, ?int $languageId)
    {
        return $query->where(function ($q) use ($languageId) {
            $q->whereHas('languages', fn ($languages) => $languages->where('languages.id', $languageId));
            // Keep pre-migration advertisements working until they are next edited.
            $q->orWhere(function ($legacy) use ($languageId) {
                $legacy->whereDoesntHave('languages')
                    ->where(fn ($language) => $language->whereNull('language_id')->orWhere('language_id', $languageId));
            });
        });
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
