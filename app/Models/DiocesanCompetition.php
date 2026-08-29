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
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'time_limit_seconds' => 'integer',
            'question_count' => 'integer',
            'scoring_rules' => 'array',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
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
}
