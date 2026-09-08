<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
    protected $fillable = ['section_id', 'name', 'description', 'color', 'icon_path'];

    public function getIconPathAttribute($value)
    {
        if (! $value) {
            return null;
        }
        if (str_starts_with($value, 'http')) {
            return $value;
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
        return $this->morphMany(Topic::class, 'topicable');
    }

    public function mockTests()
    {
        return $this->hasMany(MockTest::class);
    }
}
