<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParishCompetition extends Model
{
    use HasUuids;

    protected $fillable = [
        'parish_id',
        'created_by',
        'title',
        'description',
        'rally_pin',
        'category_id',
        'level',
        'time_limit_seconds',
        'question_count',
        'status',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'time_limit_seconds' => 'integer',
            'question_count' => 'integer',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
