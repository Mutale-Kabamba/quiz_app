<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTopicMastery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'mastery_score',
        'confidence_level',
        'questions_attempted',
        'questions_correct',
        'lessons_completed',
        'weak_concept_ids',
        'strong_concept_ids',
        'last_assessed_at',
    ];

    protected $casts = [
        'mastery_score' => 'integer',
        'questions_attempted' => 'integer',
        'questions_correct' => 'integer',
        'lessons_completed' => 'integer',
        'weak_concept_ids' => 'array',
        'strong_concept_ids' => 'array',
        'last_assessed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function topic()
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }
}
