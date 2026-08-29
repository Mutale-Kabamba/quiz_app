<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyResourceVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_resource_id',
        'version_number',
        'title',
        'subheading',
        'content_body',
        'content_sections',
        'key_terms',
        'learning_objectives',
        'changelog_notes',
        'created_by',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'content_sections' => 'array',
        'key_terms' => 'array',
        'learning_objectives' => 'array',
    ];

    public function studyResource()
    {
        return $this->belongsTo(StudyResource::class, 'study_resource_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
