<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubSection extends Model
{
    protected $fillable = ['section_id', 'name', 'description', 'color', 'icon_path', 'page_type'];

    public function isHazardPerception(): bool
    {
        return $this->page_type !== null
            ? $this->page_type === 'hazard'
            : Str::contains(Str::lower($this->name), 'hazard perception');
    }

    public function isRevisionPractice(): bool
    {
        $name = Str::lower($this->name);

        return Str::contains($name, 'revision') && Str::contains($name, 'question');
    }

    public function getIconPathAttribute($value)
    {
        if (! $value) {
            return null;
        }
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        if (! MediaStorage::isLocal()) {
            return MediaStorage::url($value);
        }

        return route('media.icons', [
            'type' => 'sub-section',
            'id' => $this->getKey(),
            'v' => $this->updated_at?->timestamp,
        ]);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function topics()
    {
        return $this->morphMany(Topic::class, 'topicable')->orderBy('position')->orderBy('id');
    }

    public function mockTests()
    {
        return $this->hasMany(MockTest::class);
    }
}
