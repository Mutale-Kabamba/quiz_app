<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_concept_id',
        'target_concept_id',
        'relationship_type',
        'pedagogical_note',
    ];

    public function sourceConcept()
    {
        return $this->belongsTo(TaxonomyConcept::class, 'source_concept_id');
    }

    public function targetConcept()
    {
        return $this->belongsTo(TaxonomyConcept::class, 'target_concept_id');
    }
}
