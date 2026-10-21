<?php

namespace App\Models;

use App\Support\MediaStorage;
use App\Support\RevisionCatalog;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    public const TYPE_QUESTIONS = 'questions';

    public const TYPE_LEARNING_PAGES = 'learning_pages';

    protected $fillable = ['topicable_type', 'topicable_id', 'name_en', 'name_ku', 'content_type', 'color', 'icon_path', 'icon_preset', 'position'];

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
            return array_key_exists($this->icon_preset ?? '', RevisionCatalog::icons())
                ? asset('design-v2/Practice/img/practice/'.$this->icon_preset.'.svg') : null;
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

    public function revisionGroup()
    {
        return $this->belongsTo(Category::class, 'revision_group_id');
    }

    public function scopeInRevisionGroup($query, int $groupId)
    {
        return $query->where(fn ($placement) => $placement
            ->where(fn ($child) => $child->where('topicable_type', Category::class)->where('topicable_id', $groupId))
            ->orWhere(fn ($entry) => $entry->where('revision_group_id', $groupId)->whereHas('questions')));
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
