<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['topicable_type', 'topicable_id', 'name_en', 'name_ku'];

    public function topicable()
    {
        return $this->morphTo();
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function contentPages()
    {
        return $this->hasMany(ContentPage::class);
    }
}
