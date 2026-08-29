<?php

namespace App\Services;

use App\Models\ConceptRelationship;
use App\Models\TaxonomyConcept;
use App\Models\TaxonomyDomain;
use App\Models\TaxonomyTrack;
use App\Models\TaxonomyTopic;
use Illuminate\Support\Collection;

class KnowledgeTaxonomyService
{
    /**
     * Get the full tree of tracks, categories, and topics
     */
    public function getFullTaxonomyTree(?string $domainSlug = null): Collection
    {
        $query = TaxonomyTrack::where('is_active', true)->with([
            'categories' => fn($q) => $q->where('is_active', true)->orderBy('display_order')->with([
                'topics' => fn($t) => $t->where('is_active', true)->orderBy('display_order')->with('concepts')
            ])
        ])->orderBy('display_order');

        if ($domainSlug) {
            $query->whereHas('domain', fn($d) => $d->where('slug', $domainSlug));
        }

        return $query->get();
    }

    /**
     * Get topic hierarchy path (Domain -> Track -> Category -> Topic)
     */
    public function getTopicBreadcrumb(TaxonomyTopic $topic): array
    {
        $category = $topic->category;
        $track = $category?->track;
        $domain = $track?->domain;

        return [
            'domain' => $domain ? ['id' => $domain->id, 'name' => $domain->name, 'slug' => $domain->slug] : null,
            'track' => $track ? ['id' => $track->id, 'name' => $track->name, 'slug' => $track->slug] : null,
            'category' => $category ? ['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug] : null,
            'topic' => ['id' => $topic->id, 'name' => $topic->name, 'slug' => $topic->slug],
        ];
    }

    /**
     * Add or update a pedagogical graph relationship between two concepts
     */
    public function linkConcepts(
        TaxonomyConcept $source,
        TaxonomyConcept $target,
        string $relationshipType = 'RELATED_TO',
        ?string $pedagogicalNote = null
    ): ConceptRelationship {
        return ConceptRelationship::updateOrCreate(
            [
                'source_concept_id' => $source->id,
                'target_concept_id' => $target->id,
                'relationship_type' => $relationshipType,
            ],
            [
                'pedagogical_note' => $pedagogicalNote,
            ]
        );
    }

    /**
     * Get all connected concepts in the pedagogical graph
     */
    public function getConceptGraph(TaxonomyConcept $concept): array
    {
        $outgoing = ConceptRelationship::where('source_concept_id', $concept->id)
            ->with('targetConcept')
            ->get()
            ->toBase()
            ->map(fn($r) => [
                'concept_id' => $r->target_concept_id,
                'name' => $r->targetConcept?->name,
                'direction' => 'OUTGOING',
                'type' => $r->relationship_type,
                'note' => $r->pedagogical_note,
            ]);

        $incoming = ConceptRelationship::where('target_concept_id', $concept->id)
            ->with('sourceConcept')
            ->get()
            ->toBase()
            ->map(fn($r) => [
                'concept_id' => $r->source_concept_id,
                'name' => $r->sourceConcept?->name,
                'direction' => 'INCOMING',
                'type' => $r->relationship_type,
                'note' => $r->pedagogical_note,
            ]);

        $prerequisites = ConceptRelationship::where('target_concept_id', $concept->id)
            ->where('relationship_type', 'PREREQUISITE_OF')
            ->with('sourceConcept')
            ->get()
            ->pluck('sourceConcept')
            ->filter();

        return [
            'concept' => $concept,
            'prerequisites' => $prerequisites,
            'connections' => $outgoing->merge($incoming),
        ];
    }
}
