<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionLockedQuestionSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_identifier',
        'round_name',
        'round_number',
        'locked_question_snapshots',
        'question_count',
        'is_locked',
        'locked_by',
        'locked_at',
    ];

    protected $casts = [
        'round_number' => 'integer',
        'question_count' => 'integer',
        'locked_question_snapshots' => 'array',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function locker()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}
