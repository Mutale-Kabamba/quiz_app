<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'duration_days',
        'description',
        'daily_schedule_manifest',
        'completion_xp_reward',
        'is_active',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'completion_xp_reward' => 'integer',
        'daily_schedule_manifest' => 'array',
        'is_active' => 'boolean',
    ];
}
