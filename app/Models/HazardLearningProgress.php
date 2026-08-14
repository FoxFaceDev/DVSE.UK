<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HazardLearningProgress extends Model
{
    protected $table = 'hazard_learning_progress';

    protected $fillable = [
        'user_id',
        'content_page_id',
        'watched_at',
    ];

    protected function casts(): array
    {
        return [
            'watched_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contentPage(): BelongsTo
    {
        return $this->belongsTo(ContentPage::class);
    }
}
