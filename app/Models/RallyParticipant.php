<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RallyParticipant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'rally_id',
        'user_id',
        'access_code',
        'status',
        'joined_at',
        'approved_at',
        'approved_by',
        'completed_at',
        'score',
        'rank',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
            'score' => 'integer',
            'rank' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function rally(): BelongsTo
    {
        return $this->belongsTo(DiocesanCompetition::class, 'rally_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereIn('status', ['approved', 'active', 'completed']);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isApproved(): bool
    {
        return in_array($this->status, ['approved', 'active', 'completed']);
    }

    public function isRemoved(): bool
    {
        return $this->status === 'removed';
    }

    public function getMaskedCode(): string
    {
        if (empty($this->access_code)) {
            return '—';
        }
        $len = strlen($this->access_code);
        if ($len <= 4) {
            return str_repeat('•', $len);
        }
        return substr($this->access_code, 0, 2) . str_repeat('•', $len - 4) . substr($this->access_code, -2);
    }
}
