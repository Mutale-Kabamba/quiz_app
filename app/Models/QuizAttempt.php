<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'category_id',
        'level',
        'mode',
        'score',
        'total_questions',
        'correct_answers_count',
        'time_taken_seconds',
        'completed_at',
        'is_synced',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'is_synced' => 'boolean',
            'level' => 'integer',
            'score' => 'integer',
            'total_questions' => 'integer',
            'correct_answers_count' => 'integer',
            'time_taken_seconds' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
