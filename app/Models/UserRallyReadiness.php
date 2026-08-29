<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRallyReadiness extends Model
{
    use HasFactory;

    protected $table = 'user_rally_readiness';

    protected $fillable = [
        'user_id',
        'rally_id',
        'overall_readiness_percentage',
        'scripture_readiness',
        'catechism_readiness',
        'history_readiness',
        'saints_readiness',
        'doctrine_readiness',
        'training_questions_answered',
        'last_trained_at',
    ];

    protected $casts = [
        'overall_readiness_percentage' => 'integer',
        'scripture_readiness' => 'integer',
        'catechism_readiness' => 'integer',
        'history_readiness' => 'integer',
        'saints_readiness' => 'integer',
        'doctrine_readiness' => 'integer',
        'training_questions_answered' => 'integer',
        'last_trained_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rally(): BelongsTo
    {
        return $this->belongsTo(RallyPreparation::class, 'rally_id');
    }
}
