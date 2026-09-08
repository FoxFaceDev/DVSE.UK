<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'description', 'color', 'icon_path'];

    public function getIconPathAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;

        return route('media.icons', [
            'type' => 'section',
            'id' => $this->getKey(),
            'v' => $this->updated_at?->timestamp,
        ]);
    }

    public function subSections()
    {
        return $this->hasMany(SubSection::class);
    }

    public function topics()
    {
        return $this->morphMany(Topic::class, 'topicable');
    }
}
