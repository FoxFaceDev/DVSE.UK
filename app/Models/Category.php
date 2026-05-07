<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['sub_section_id', 'name_en', 'name_ku'];

    public function subSection()
    {
        return $this->belongsTo(SubSection::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
