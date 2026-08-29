<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'track_id',
        'level_id',
        'estimated_total_hours',
        'xp_completion_bonus',
        'is_published',
    ];

    protected $casts = [
        'estimated_total_hours' => 'integer',
        'xp_completion_bonus' => 'integer',
        'is_published' => 'boolean',
    ];

    public function track()
    {
        return $this->belongsTo(TaxonomyTrack::class, 'track_id');
    }

    public function level()
    {
        return $this->belongsTo(FormationLevel::class, 'level_id');
    }

    public function steps()
    {
        return $this->hasMany(LearningPathStep::class, 'learning_path_id')->orderBy('step_number');
    }
}
