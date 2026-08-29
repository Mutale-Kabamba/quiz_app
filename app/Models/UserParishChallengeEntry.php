<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserParishChallengeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'user_id',
        'parish_id',
        'contribution_xp',
        'tasks_completed',
        'has_claimed_reward',
    ];

    protected $casts = [
        'contribution_xp' => 'integer',
        'tasks_completed' => 'integer',
        'has_claimed_reward' => 'boolean',
    ];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(ParishFormationChallenge::class, 'challenge_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }
}
