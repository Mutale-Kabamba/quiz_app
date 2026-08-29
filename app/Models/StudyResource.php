<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyResource extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'track_id',
        'category_id',
        'topic_id',
        'subtopic_id',
        'concept_id',
        'level_id',
        'age_band_id',
        'title',
        'slug',
        'subheading',
        'resource_type',
        'estimated_read_minutes',
        'difficulty_rating',
        'language_code',
        'why_this_matters',
        'learning_objectives',
        'key_idea',
        'content_body',
        'content_sections',
        'key_terms',
        'common_misconceptions',
        'practical_application',
        'reflection_questions',
        'revision_points',
        'keywords',
        'prerequisite_resource_ids',
        'status',
        'ownership_scope',
        'visibility',
        'author_id',
        'reviewer_id',
        'theological_reviewer_id',
        'reviewed_at',
        'published_at',
        'current_version',
    ];

    protected $casts = [
        'learning_objectives' => 'array',
        'content_sections' => 'array',
        'key_terms' => 'array',
        'common_misconceptions' => 'array',
        'reflection_questions' => 'array',
        'revision_points' => 'array',
        'keywords' => 'array',
        'prerequisite_resource_ids' => 'array',
        'estimated_read_minutes' => 'integer',
        'difficulty_rating' => 'integer',
        'current_version' => 'integer',
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function track()
    {
        return $this->belongsTo(TaxonomyTrack::class, 'track_id');
    }

    public function category()
    {
        return $this->belongsTo(TaxonomyCategory::class, 'category_id');
    }

    public function topic()
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }

    public function subtopic()
    {
        return $this->belongsTo(TaxonomySubtopic::class, 'subtopic_id');
    }

    public function concept()
    {
        return $this->belongsTo(TaxonomyConcept::class, 'concept_id');
    }

    public function level()
    {
        return $this->belongsTo(FormationLevel::class, 'level_id');
    }

    public function ageBand()
    {
        return $this->belongsTo(AgeBand::class, 'age_band_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function theologicalReviewer()
    {
        return $this->belongsTo(User::class, 'theological_reviewer_id');
    }

    public function versions()
    {
        return $this->hasMany(StudyResourceVersion::class, 'study_resource_id')->orderByDesc('version_number');
    }

    public function references()
    {
        return $this->belongsToMany(CatholicReference::class, 'content_references', 'study_resource_id', 'catholic_reference_id')
            ->withPivot('relevance_note')
            ->withTimestamps();
    }
}
