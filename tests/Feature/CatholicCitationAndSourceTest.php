<?php

namespace Tests\Feature;

use App\Models\CatholicReference;
use App\Models\CatholicSource;
use App\Services\CatholicCitationService;
use Database\Seeders\CatholicKnowledgeTaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatholicCitationAndSourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatholicKnowledgeTaxonomySeeder::class);
    }

    public function test_validates_and_structures_ccc_citations(): void
    {
        $service = app(CatholicCitationService::class);

        $validCcc = $service->validateAndStructureCitation('CCC', '1213-1216');
        $this->assertTrue($validCcc['is_valid']);
        $this->assertEquals('CCC 1213-1216', $validCcc['structured_reference']['citation_label']);
        $this->assertEquals('1213-1216', $validCcc['structured_reference']['verse_or_paragraph_range']);

        $singleCcc = $service->validateAndStructureCitation('CCC', '#1212');
        $this->assertTrue($singleCcc['is_valid']);
        $this->assertEquals('CCC 1212', $singleCcc['structured_reference']['citation_label']);

        // Out of range (CCC max is 2865)
        $invalidCcc = $service->validateAndStructureCitation('CCC', '9999');
        $this->assertFalse($invalidCcc['is_valid']);
    }

    public function test_validates_and_structures_scripture_citations(): void
    {
        $service = app(CatholicCitationService::class);

        $validScripture = $service->validateAndStructureCitation('RSVCE', 'John 3:16');
        $this->assertTrue($validScripture['is_valid']);
        $this->assertEquals('John 3:16', $validScripture['structured_reference']['citation_label']);
        $this->assertEquals('John', $validScripture['structured_reference']['book_or_volume']);
        $this->assertEquals(3, $validScripture['structured_reference']['chapter_or_section']);
        $this->assertEquals('16', $validScripture['structured_reference']['verse_or_paragraph_range']);

        $passage = $service->validateAndStructureCitation('RSVCE', 'Romans 12:1-2');
        $this->assertTrue($passage['is_valid']);
        $this->assertEquals('Romans 12:1-2', $passage['structured_reference']['citation_label']);
    }

    public function test_creates_persisted_catholic_reference(): void
    {
        $service = app(CatholicCitationService::class);
        $ref = $service->getOrCreateReference('YOUCAT', '194', 'What is Confirmation?');

        $this->assertNotNull($ref);
        $this->assertEquals('YOUCAT #194', $ref->citation_label);
        $this->assertDatabaseHas('catholic_references', ['citation_label' => 'YOUCAT #194']);
    }
}
