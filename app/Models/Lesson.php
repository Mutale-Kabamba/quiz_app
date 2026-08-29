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
}
