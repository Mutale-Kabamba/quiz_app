<?php

namespace Tests\Feature;

use App\Models\QuestionBankItem;
use App\Models\TaxonomyTrack;
use App\Models\User;
use App\Services\DuplicateDetectionService;
use App\Services\QuestionBankService;
use Database\Seeders\CatholicKnowledgeTaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniversalQuestionBankTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatholicKnowledgeTaxonomySeeder::class);
    }

    public function test_creates_question_with_options_and_immutable_version_1(): void
    {
        $track = TaxonomyTrack::first();
        $service = app(QuestionBankService::class);

        $question = $service->createQuestion([
            'track_id' => $track->id,
            'question_type' => 'MULTIPLE_CHOICE',
            'question_text' => 'Which sacrament is the source and summit of Christian life?',
            'explanation' => 'The Eucharist is the source and summit of Christian life (CCC 1324).',
            'reference_citation' => 'CCC 1324',
            'editorial_difficulty' => 'EASY',
            'status' => 'APPROVED',
        ], [
            ['option_key' => 'A', 'option_text' => 'Baptism', 'is_correct' => false],
            ['option_key' => 'B', 'option_text' => 'The Holy Eucharist', 'is_correct' => true],
            ['option_key' => 'C', 'option_text' => 'Confirmation', 'is_correct' => false],
            ['option_key' => 'D', 'option_text' => 'Holy Orders', 'is_correct' => false],
        ]);

        $this->assertInstanceOf(QuestionBankItem::class, $question);
        $this->assertDatabaseHas('question_bank_items', ['id' => $question->id, 'current_version' => 1]);
        $this->assertEquals(4, $question->options()->count());
        $this->assertEquals(1, $question->versions()->count());
        $this->assertNotNull($question->duplicate_similarity_hash);
    }

    public function test_duplicate_detection_flags_semantic_and_exact_similarity(): void
    {
        $track = TaxonomyTrack::first();
        $service = app(QuestionBankService::class);
        $duplicateService = app(DuplicateDetectionService::class);

        $q1 = $service->createQuestion([
            'track_id' => $track->id,
            'question_text' => 'Who is the Mother of Jesus Christ in Catholic teaching?',
            'status' => 'PUBLISHED',
        ], [
            ['option_key' => 'A', 'option_text' => 'Mary', 'is_correct' => true],
            ['option_key' => 'B', 'option_text' => 'Martha', 'is_correct' => false],
        ]);

        // Same question with punctuation change
        $duplicates = $duplicateService->findPotentialDuplicates(
            'Who is the mother of Jesus Christ in Catholic teaching?!',
            $track->id
        );

        $this->assertNotEmpty($duplicates);
        $this->assertEquals($q1->id, $duplicates->first()['question']->id);
    }

    public function test_records_attempt_statistics_and_updates_empirical_difficulty(): void
    {
        $track = TaxonomyTrack::first();
        $service = app(QuestionBankService::class);

        $question = $service->createQuestion([
            'track_id' => $track->id,
            'question_text' => 'How many sacraments are instituted by Christ?',
            'status' => 'PUBLISHED',
        ], [
            ['option_key' => 'A', 'option_text' => '7', 'is_correct' => true],
            ['option_key' => 'B', 'option_text' => '10', 'is_correct' => false],
        ]);

        // Simulate 10 attempts: 8 correct, 2 incorrect, 5.0 seconds avg
        for ($i = 0; $i < 8; $i++) {
            $service->recordAttemptStats($question->fresh(), true, 5.0);
        }
        for ($i = 0; $i < 2; $i++) {
            $service->recordAttemptStats($question->fresh(), false, 7.0);
        }

        $question->refresh();
        $this->assertEquals(10, $question->times_answered);
        $this->assertEquals(8, $question->times_correct);
        $this->assertEquals(0.80, $question->empirical_difficulty);
        $this->assertEquals(100, $question->health_score);
    }
}
