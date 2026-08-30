<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasUuids;

    protected $fillable = [
        'category_id',
        'series_identifier',
        'series_title',
        'series_order',
        'is_progressive',
        'title',
        'slug',
        'subheading',
        'summary_takeaways',
        'content_sections',
        'key_terms',
        'estimated_read_minutes',
        'difficulty',
        'scripture_citations',
        'catechism_citations',
        'display_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'summary_takeaways' => 'array',
            'content_sections' => 'array',
            'key_terms' => 'array',
            'estimated_read_minutes' => 'integer',
            'difficulty' => 'integer',
            'display_order' => 'integer',
            'series_order' => 'integer',
            'is_progressive' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Check if this lesson is part of a multi-part series
     */
    public function isPartOfSeries(): bool
    {
        return !empty($this->series_identifier);
    }

    /**
     * Get all lessons in the same series
     */
    public function getSeriesLessons()
    {
        if (!$this->isPartOfSeries()) {
            return collect([$this]);
        }

        return self::where('series_identifier', $this->series_identifier)
            ->where('status', 'published')
            ->orderBy('series_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Check if user has completed prerequisites for this progressive lesson
     */
    public function arePrerequisitesMet(?User $user): bool
    {
        if (!$user || !$this->isPartOfSeries() || !$this->is_progressive || ($this->series_order ?? 1) <= 1) {
            return true;
        }

        $precedingLessonIds = self::where('series_identifier', $this->series_identifier)
            ->where('status', 'published')
            ->where('series_order', '<', $this->series_order)
            ->pluck('id');

        if ($precedingLessonIds->isEmpty()) {
            return true;
        }

        $completedCount = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $precedingLessonIds)
            ->where('is_completed', true)
            ->count();

        return $completedCount >= $precedingLessonIds->count();
    }
}
