<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSpacedQuestionReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'concept_id',
        'topic_id',
        'mistake_count',
        'consecutive_correct',
        'interval_days',
        'last_reviewed_at',
        'next_review_date',
        'is_mastered',
    ];

    protected $casts = [
        'mistake_count' => 'integer',
        'consecutive_correct' => 'integer',
        'interval_days' => 'integer',
        'last_reviewed_at' => 'datetime',
        'next_review_date' => 'date',
        'is_mastered' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function concept(): BelongsTo
    {
        return $this->belongsTo(TaxonomyConcept::class, 'concept_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionBankItem::class, 'question_id');
    }
}
