<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    protected $fillable = ['question_id', 'text_en', 'text_ku', 'image_path', 'translations', 'is_correct'];

    protected $casts = [
        'is_correct' => 'boolean',
        'translations' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getImagePathAttribute($value)
    {
        return MediaStorage::url($value);
    }
}
