<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parish extends Model
{
    protected $fillable = ['deanery_id', 'name', 'location'];

    public function deanery(): BelongsTo
    {
        return $this->belongsTo(Deanery::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ParishEvent::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(ParishAnnouncement::class);
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(ParishCompetition::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(ParishTransfer::class, 'from_parish_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(ParishTransfer::class, 'to_parish_id');
    }
}
