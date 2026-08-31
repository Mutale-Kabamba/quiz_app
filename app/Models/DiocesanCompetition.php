<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiocesanCompetition extends Model
{
    use HasUuids;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'competition_type',
        'scope_type',
        'deanery_id',
        'parish_id',
        'category_id',
        'rally_pin',
        'level',
        'time_limit_seconds',
        'question_count',
        'status',
        'scoring_rules',
        'start_time',
        'end_time',
        'registration_open_at',
        'registration_close_at',
        'join_requests_enabled',
        'is_public',
        'max_participants',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'time_limit_seconds' => 'integer',
            'question_count' => 'integer',
            'max_participants' => 'integer',
            'join_requests_enabled' => 'boolean',
            'is_public' => 'boolean',
            'scoring_rules' => 'array',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'registration_open_at' => 'datetime',
            'registration_close_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deanery(): BelongsTo
    {
        return $this->belongsTo(Deanery::class);
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function participants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RallyParticipant::class, 'rally_id');
    }

    public function approvedParticipants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RallyParticipant::class, 'rally_id')->whereIn('status', ['approved', 'active', 'completed']);
    }

    public function joinRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RallyJoinRequest::class, 'rally_id');
    }

    public function pendingJoinRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RallyJoinRequest::class, 'rally_id')->where('status', 'pending');
    }

    public function isDioceseScope(): bool
    {
        return strtolower($this->scope_type ?? 'diocese') === 'diocese';
    }

    public function isDeaneryScope(): bool
    {
        return strtolower($this->scope_type ?? '') === 'deanery';
    }

    public function isParishScope(): bool
    {
        return strtolower($this->scope_type ?? '') === 'parish';
    }

    public function isCustomScope(): bool
    {
        return strtolower($this->scope_type ?? '') === 'custom';
    }

    public function isLiveNow(): bool
    {
        if (in_array(strtolower($this->status), ['draft', 'paused', 'cancelled', 'completed', 'closed'])) {
            return false;
        }

        $now = now();
        if ($this->start_time && $now->lt($this->start_time)) {
            return false;
        }
        if ($this->end_time && $now->gt($this->end_time)) {
            return false;
        }

        return true;
    }

    public function isRegistrationOpen(): bool
    {
        if (in_array(strtolower($this->status), ['completed', 'cancelled', 'closed'])) {
            return false;
        }

        $now = now();
        if ($this->registration_open_at && $now->lt($this->registration_open_at)) {
            return false;
        }
        if ($this->registration_close_at && $now->gt($this->registration_close_at)) {
            return false;
        }

        return true;
    }
}

