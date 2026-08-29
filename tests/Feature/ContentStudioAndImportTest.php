<?php

namespace Tests\Feature;

use App\Models\ContentImportJob;
use App\Models\QuestionBankItem;
use App\Models\TaxonomyTrack;
use App\Models\User;
use App\Services\ContentGapAnalysisService;
use App\Services\ContentImportExportService;
use Database\Seeders\CatholicKnowledgeTaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentStudioAndImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatholicKnowledgeTaxonomySeeder::class);

        $this->admin = User::create([
            'name' => 'Content Administrator',
            'phone' => '+260970000013',
            'email' => 'admin@livingstonediocese.org',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'approved',
        ]);
    }

    public function test_bulk_question_import_with_duplicate_skipping(): void
    {
        $track = TaxonomyTrack::first();
        $service = app(ContentImportExportService::class);

        $importJob = ContentImportJob::create([
            'file_name' => 'catechism_questions.csv',
            'format' => 'CSV',
            'target_entity' => 'QUESTIONS',
            'total_rows' => 3,
            'uploaded_by' => $this->admin->id,
        ]);

        $rows = [
            [
                'track_id' => $track->id,
                'question_text' => 'What is the First Sacrament received by a Christian?',
                'option_a' => 'Baptism',
                'option_b' => 'Confirmation',
                'option_c' => 'Eucharist',
                'option_d' => 'Matrimony',
                'correct_option' => 'A',
                'explanation' => 'Baptism is the gateway to the sacraments.',
            ],
            // Duplicate of row 1
            [
                'track_id' => $track->id,
                'question_text' => 'What is the first sacrament received by a christian?',
                'option_a' => 'Baptism',
                'option_b' => 'Confirmation',
                'correct_option' => 'A',
            ],
            [
                'track_id' => $track->id,
                'question_text' => 'Who is the Bishop of Rome?',
                'option_a' => 'The Pope',
                'option_b' => 'The Patriarch',
                'correct_option' => 'A',
            ],
        ];

        $service->importQuestionsChunk($importJob, $rows, $this->admin);

        $importJob->refresh();
        $this->assertEquals(3, $importJob->processed_rows);
        $this->assertEquals(2, $importJob->successful_rows);
        $this->assertEquals(1, $importJob->duplicate_rows);
        $this->assertEquals(0, $importJob->failed_rows);
    }

    public function test_content_gap_analysis_identifies_uncovered_topics(): void
    {
        $analysis = app(ContentGapAnalysisService::class)->analyzeCoverage();

        $this->assertArrayHasKey('total_gaps', $analysis);
        $this->assertArrayHasKey('matrix', $analysis);
        $this->assertNotEmpty($analysis['matrix']);

        // Since newly seeded topics don't have questions yet, gaps should be detected
        $this->assertGreaterThan(0, $analysis['total_gaps']);
    }
}
