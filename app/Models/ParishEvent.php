<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParishEvent extends Model
{
    use HasUuids;

    protected $fillable = [
        'parish_id',
        'created_by',
        'title',
        'description',
        'event_type',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'organizer',
        'requires_registration',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'requires_registration' => 'boolean',
            'capacity' => 'integer',
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
}
