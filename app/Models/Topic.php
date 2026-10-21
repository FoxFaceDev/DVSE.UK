<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    public const TYPE_QUESTIONS = 'questions';

    public const TYPE_LEARNING_PAGES = 'learning_pages';

    protected $fillable = ['topicable_type', 'topicable_id', 'name_en', 'name_ku', 'content_type', 'color', 'icon_path', 'position'];

    protected static function booted(): void
    {
        static::creating(function (Topic $category) {
            if ($category->position) {
                return;
            }

            $category->position = (int) static::query()
                ->where('topicable_type', $category->topicable_type)
                ->where('topicable_id', $category->topicable_id)
                ->max('position') + 1;
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('id');
    }

    public function getIconPathAttribute($value)
    {
        if (! $value) {
            return null;
        }
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        if (! MediaStorage::isLocal()) {
            return MediaStorage::url($value);
        }

        return route('media.icons', ['type' => 'category', 'id' => $this->getKey(), 'v' => $this->updated_at?->timestamp]);
    }

    public function isQuestionCategory(): bool
    {
        return $this->content_type === self::TYPE_QUESTIONS;
    }

    public function isLearningCategory(): bool
    {
        return $this->content_type === self::TYPE_LEARNING_PAGES;
    }

    public function scopeQuestionCategories($query)
    {
        return $query->where('content_type', self::TYPE_QUESTIONS);
    }

    public function scopeLearningCategories($query)
    {
        return $query->where('content_type', self::TYPE_LEARNING_PAGES);
    }

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
