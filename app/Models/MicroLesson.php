<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MicroLesson extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'topic_id',
        'category_id',
        'title',
        'slug',
        'hook_question',
        'content_body',
        'takeaways',
        'flashcard_ids',
        'question_ids',
        'reference_citation',
        'read_time_minutes',
        'xp_reward',
        'is_published',
    ];

    protected $casts = [
        'takeaways' => 'array',
        'flashcard_ids' => 'array',
        'question_ids' => 'array',
        'is_published' => 'boolean',
        'read_time_minutes' => 'integer',
        'xp_reward' => 'integer',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(UserMicroLessonCompletion::class, 'micro_lesson_id');
    }

    public function getSubheadingAttribute(): ?string
    {
        return $this->hook_question;
    }

    public function getEstimatedReadMinutesAttribute(): int
    {
        return $this->read_time_minutes ?? 4;
    }

    public function getDifficultyAttribute(): int
    {
        return 1;
    }

    public function getScriptureCitationsAttribute(): ?string
    {
        return $this->reference_citation;
    }

    public function getSummaryTakeawaysAttribute(): array
    {
        return $this->takeaways ?? [];
    }

    public function getContentSectionsAttribute(): array
    {
        return [
            [
                'heading' => 'Catechetical Reflection & Doctrine',
                'body' => $this->content_body,
                'scripture_quote' => null,
                'catechism_quote' => $this->reference_citation,
            ]
        ];
    }

    public function getReflectionQuestionAttribute(): ?string
    {
        return $this->hook_question;
    }
}
