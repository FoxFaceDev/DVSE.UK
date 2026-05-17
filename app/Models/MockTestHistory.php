<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTestHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'score',
        'total_questions',
        'passed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
