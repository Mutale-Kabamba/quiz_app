<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyChallenge extends Model
{
    protected $fillable = [
        'challenge_date',
        'title',
        'description',
        'question_ids',
        'xp_reward',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'challenge_date' => 'date',
            'question_ids' => 'array',
            'xp_reward' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function participations(): HasMany
    {
        return $this->hasMany(UserChallengeParticipation::class);
    }
}
