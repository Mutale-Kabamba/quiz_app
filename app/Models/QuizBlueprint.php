<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizBlueprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'question_count',
        'time_limit_seconds',
        'level_id',
        'track_rules',
        'difficulty_distribution',
        'bloom_distribution',
        'unseen_question_ratio',
        'is_active',
    ];

    protected $casts = [
        'question_count' => 'integer',
        'time_limit_seconds' => 'integer',
        'track_rules' => 'array',
        'difficulty_distribution' => 'array',
        'bloom_distribution' => 'array',
        'unseen_question_ratio' => 'integer',
        'is_active' => 'boolean',
    ];

    public function level()
    {
        return $this->belongsTo(FormationLevel::class, 'level_id');
    }
}
