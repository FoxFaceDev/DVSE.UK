<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name_en', 'name_ku'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
