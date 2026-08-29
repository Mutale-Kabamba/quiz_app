<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPathStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_path_id',
        'step_number',
        'title',
        'description',
        'study_resource_id',
        'topic_id',
        'quiz_blueprint_id',
        'is_required',
    ];

    protected $casts = [
        'step_number' => 'integer',
        'is_required' => 'boolean',
    ];

    public function learningPath()
    {
        return $this->belongsTo(LearningPath::class, 'learning_path_id');
    }

    public function studyResource()
    {
        return $this->belongsTo(StudyResource::class, 'study_resource_id');
    }

    public function topic()
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }

    public function quizBlueprint()
    {
        return $this->belongsTo(QuizBlueprint::class, 'quiz_blueprint_id');
    }
}
