<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['sub_section_id', 'name_en', 'name_ku', 'page_type'];

    public function isRevisionPractice(): bool
    {
        return $this->page_type !== null ? $this->page_type === 'revision'
            : (Str::contains(Str::lower($this->name_en), 'revision') && Str::contains(Str::lower($this->name_en), 'question'));
    }

    public function subSection()
    {
        return $this->belongsTo(SubSection::class);
    }

    public function topics()
    {
        return $this->morphMany(Topic::class, 'topicable')->orderBy('position')->orderBy('id');
    }
}
