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
        'series_identifier',
        'series_title',
        'series_order',
        'is_progressive',
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
        'series_order' => 'integer',
        'is_progressive' => 'boolean',
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

    /**
     * Check if this micro lesson is part of a multi-part series
     */
    public function isPartOfSeries(): bool
    {
        return !empty($this->series_identifier);
    }

    /**
     * Get all micro lessons in the same series
     */
    public function getSeriesLessons()
    {
        if (!$this->isPartOfSeries()) {
            return collect([$this]);
        }

        return self::where('series_identifier', $this->series_identifier)
            ->where('is_published', true)
            ->orderBy('series_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Check if user has completed prerequisites for this progressive micro lesson
     */
    public function arePrerequisitesMet(?User $user): bool
    {
        if (!$user || !$this->isPartOfSeries() || !$this->is_progressive || ($this->series_order ?? 1) <= 1) {
            return true;
        }

        $precedingIds = self::where('series_identifier', $this->series_identifier)
            ->where('is_published', true)
            ->where('series_order', '<', $this->series_order)
            ->pluck('id');

        if ($precedingIds->isEmpty()) {
            return true;
        }

        $completedCount = UserMicroLessonCompletion::where('user_id', $user->id)
            ->whereIn('micro_lesson_id', $precedingIds)
            ->count();

        return $completedCount >= $precedingIds->count();
    }
}
