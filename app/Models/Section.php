<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'color', 'icon_path'];

    public function getIconPathAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        return asset($value);
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
