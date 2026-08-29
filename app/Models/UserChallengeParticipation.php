<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserChallengeParticipation extends Model
{
    protected $fillable = [
        'user_id',
        'daily_challenge_id',
        'score',
        'xp_earned',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'xp_earned' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyChallenge(): BelongsTo
    {
        return $this->belongsTo(DailyChallenge::class);
    }
}
