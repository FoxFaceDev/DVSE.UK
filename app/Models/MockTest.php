<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MockTest extends Model
{
    protected $fillable = ['sub_section_id', 'name', 'type', 'description', 'question_count', 'video_question_count', 'duration_minutes', 'pass_mark', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function subSection()
    {
        return $this->belongsTo(SubSection::class);
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class);
    }
}
