<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'category_id', 'text_en', 'text_ku', 'image_path',
        'explanation_en', 'explanation_ku'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImagePathAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        
        // Ensure the path starts with /storage/ for the browser to find it from the root
        $path = str_replace('/storage/', '', $value);
        return '/storage/' . ltrim($path, '/');
    }

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
}
