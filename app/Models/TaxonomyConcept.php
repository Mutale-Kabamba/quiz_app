<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxonomyConcept extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'subtopic_id',
        'name',
        'slug',
        'summary_definition',
        'canonical_terms',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'canonical_terms' => 'array',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function topic()
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }

    public function subtopic()
    {
        return $this->belongsTo(TaxonomySubtopic::class, 'subtopic_id');
    }

    public function outgoingRelationships()
    {
        return $this->hasMany(ConceptRelationship::class, 'source_concept_id');
    }

    public function incomingRelationships()
    {
        return $this->hasMany(ConceptRelationship::class, 'target_concept_id');
    }

    public function relatedConcepts()
    {
        return $this->belongsToMany(TaxonomyConcept::class, 'concept_relationships', 'source_concept_id', 'target_concept_id')
            ->withPivot('relationship_type', 'pedagogical_note')
            ->withTimestamps();
    }

    public function studyResources()
    {
        return $this->hasMany(StudyResource::class, 'concept_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionBankItem::class, 'concept_id');
    }
}
