<?php

namespace Tests\Feature;

use App\Models\TaxonomyCategory;
use App\Models\TaxonomyConcept;
use App\Models\TaxonomyDomain;
use App\Models\TaxonomyTopic;
use App\Models\TaxonomyTrack;
use App\Services\KnowledgeTaxonomyService;
use Database\Seeders\CatholicKnowledgeTaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeTaxonomyAndGraphTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatholicKnowledgeTaxonomySeeder::class);
    }

    public function test_taxonomy_seeder_populates_30_catholic_tracks(): void
    {
        $this->assertEquals(30, TaxonomyTrack::count());
        $this->assertDatabaseHas('taxonomy_tracks', ['code' => 'SCRIPTURE', 'name' => 'Holy Scripture']);
        $this->assertDatabaseHas('taxonomy_tracks', ['code' => 'YOUCAT']);
        $this->assertDatabaseHas('taxonomy_tracks', ['code' => 'CCC']);
        $this->assertDatabaseHas('taxonomy_tracks', ['code' => 'AFRICAN_HISTORY', 'name' => 'African Church History']);
        $this->assertDatabaseHas('taxonomy_tracks', ['code' => 'ZAMBIAN_HISTORY', 'name' => 'Zambian Catholic History']);
    }

    public function test_knowledge_taxonomy_service_fetches_full_tree_and_breadcrumbs(): void
    {
        $service = app(KnowledgeTaxonomyService::class);
        $tree = $service->getFullTaxonomyTree();

        $this->assertNotEmpty($tree);
        $sacramentsTrack = $tree->firstWhere('code', 'SACRAMENTS');
        $this->assertNotNull($sacramentsTrack);
        $this->assertNotEmpty($sacramentsTrack->categories);

        $baptismTopic = TaxonomyTopic::where('slug', 'sacrament-of-baptism')->first();
        $this->assertNotNull($baptismTopic);

        $breadcrumb = $service->getTopicBreadcrumb($baptismTopic);
        $this->assertEquals('Catholic Formation & Catechesis', $breadcrumb['domain']['name']);
        $this->assertEquals('Sacraments of the Church', $breadcrumb['track']['name']);
        $this->assertEquals('Sacraments of Christian Initiation', $breadcrumb['category']['name']);
        $this->assertEquals('Sacrament of Baptism', $breadcrumb['topic']['name']);
    }

    public function test_concept_graph_links_and_traversal(): void
    {
        $service = app(KnowledgeTaxonomyService::class);

        $baptismTopic = TaxonomyTopic::where('slug', 'sacrament-of-baptism')->first();
        $originalSin = TaxonomyConcept::where('slug', 'original-sin-cleansing')->first();
        $grace = TaxonomyConcept::where('slug', 'sanctifying-grace-infusion')->first();

        $link = $service->linkConcepts($originalSin, $grace, 'PREREQUISITE_OF', 'Must be cleansed before receiving adoption');
        $this->assertDatabaseHas('concept_relationships', [
            'source_concept_id' => $originalSin->id,
            'target_concept_id' => $grace->id,
            'relationship_type' => 'PREREQUISITE_OF',
        ]);

        $graph = $service->getConceptGraph($grace);
        $this->assertEquals(1, $graph['prerequisites']->count());
        $this->assertEquals('Cleansing of Original Sin', $graph['prerequisites']->first()->name);
    }
}
