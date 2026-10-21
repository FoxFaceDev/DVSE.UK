<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public const HOME_KEYS = ['theory', 'find_instructor', 'join_rivex', 'become_instructor'];

    protected $fillable = ['home_key', 'name', 'description', 'color', 'icon_path'];

    public function getIconPathAttribute($value)
    {
        if (! $value) {
            return null;
        }
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        if (self::isBundledIcon($value)) {
            return asset(ltrim($value, '/'));
        }

        if (! MediaStorage::isLocal()) {
            return MediaStorage::url($value);
        }

        return route('media.icons', [
            'type' => 'section',
            'id' => $this->getKey(),
            'v' => $this->updated_at?->timestamp,
        ]);
    }

    public static function isBundledIcon(?string $path): bool
    {
        return is_string($path) && str_starts_with(ltrim($path, '/'), 'design-v2/');
    }

    public function isHomeSection(): bool
    {
        return in_array($this->home_key, self::HOME_KEYS, true);
    }

    public function scopeHomeSections($query)
    {
        return $query->whereIn('home_key', self::HOME_KEYS);
    }

    public function subSections()
    {
        return $this->hasMany(SubSection::class);
    }

    public function topics()
    {
        return $this->morphMany(Topic::class, 'topicable')->orderBy('position')->orderBy('id');
    }
}
