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

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
}
