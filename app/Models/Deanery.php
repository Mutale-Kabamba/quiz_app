<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Deanery extends Model
{
    protected $fillable = ['name', 'code'];

    public function parishes(): HasMany
    {
        return $this->hasMany(Parish::class);
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Parish::class);
    }
}
