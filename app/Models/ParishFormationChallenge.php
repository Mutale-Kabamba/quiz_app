<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParishFormationChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'parish_id',
        'challenger_parish_id',
        'title',
        'description',
        'topic_id',
        'track_id',
        'start_date',
        'end_date',
        'target_mastery_percentage',
        'target_youth_count',
        'xp_reward_pool',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_mastery_percentage' => 'integer',
        'target_youth_count' => 'integer',
        'xp_reward_pool' => 'integer',
    ];

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class, 'parish_id');
    }

    public function challengerParish(): BelongsTo
    {
        return $this->belongsTo(Parish::class, 'challenger_parish_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(TaxonomyTrack::class, 'track_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(UserParishChallengeEntry::class, 'challenge_id');
    }
}
