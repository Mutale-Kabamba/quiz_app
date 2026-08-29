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

    public function chairpersons(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'chairperson');
    }

    public function approvedYouths(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'youth')->where('status', 'approved');
    }
}
