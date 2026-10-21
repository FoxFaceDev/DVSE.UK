<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Model;

class InstructorProfile extends Model
{
    protected $fillable = ['postcode', 'latitude', 'longitude', 'hourly_rate', 'gearboxes', 'languages', 'available_days', 'gender', 'bio', 'photo_path', 'is_published', 'contact_method'];

    protected $casts = ['gearboxes' => 'array', 'languages' => 'array', 'available_days' => 'array', 'is_published' => 'boolean', 'is_approved' => 'boolean', 'is_premium' => 'boolean', 'hourly_rate' => 'decimal:2', 'latitude' => 'float', 'longitude' => 'float'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (in_array($this->photo_path, [
            'design-v2/Instructors/img/instructors/sara.webp',
            'design-v2/Instructors/img/instructors/daniel.svg',
            'design-v2/Instructors/img/instructors/lana.svg',
        ], true)) {
            return asset($this->photo_path);
        }

        return MediaStorage::url($this->photo_path);
    }
}
