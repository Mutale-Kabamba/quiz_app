<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DifficultyTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'weight',
        'target_accuracy_min',
        'target_accuracy_max',
    ];
}
