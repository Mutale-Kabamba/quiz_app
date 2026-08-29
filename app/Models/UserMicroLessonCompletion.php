<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMicroLessonCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'micro_lesson_id',
        'quiz_score',
        'quiz_total',
        'xp_earned',
        'completed_at',
    ];

    protected $casts = [
        'quiz_score' => 'integer',
        'quiz_total' => 'integer',
        'xp_earned' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function microLesson(): BelongsTo
    {
        return $this->belongsTo(MicroLesson::class, 'micro_lesson_id');
    }
}
