<?php

namespace Tests\Feature;

use App\Models\QuestionBankItem;
use App\Models\StudyResource;
use App\Models\TaxonomyTrack;
use App\Models\User;
use App\Services\ContentPipelineService;
use App\Services\QuestionBankService;
use Database\Seeders\CatholicKnowledgeTaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentPipelineAndReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $reviewer;
    protected User $author;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatholicKnowledgeTaxonomySeeder::class);

        $this->reviewer = User::create([
            'name' => 'Theological Censor / Reviewer',
            'phone' => '+260970000010',
            'email' => 'reviewer@livingstonediocese.org',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'approved',
        ]);

        $this->author = User::create([
            'name' => 'Catechist Author',
            'phone' => '+260970000011',
            'email' => 'author@livingstonediocese.org',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'approved',
        ]);
    }

    public function test_content_lifecycle_from_draft_to_review_to_published(): void
    {
        $track = TaxonomyTrack::first();
        $questionService = app(QuestionBankService::class);
        $pipeline = app(ContentPipelineService::class);

        // 1. Create Draft Question
        $question = $questionService->createQuestion([
            'track_id' => $track->id,
            'question_text' => 'What is the biblical theme of the Book of Exodus?',
            'status' => 'DRAFT',
        ], [
            ['option_key' => 'A', 'option_text' => 'Liberation and Covenant', 'is_correct' => true],
            ['option_key' => 'B', 'option_text' => 'Creation', 'is_correct' => false],
        ], $this->author);

        $this->assertEquals('DRAFT', $question->status);

        // 2. Submit for Review
        $pipeline->submitForReview($question, $this->author, 'Ready for theological review');
        $this->assertEquals('UNDER_REVIEW', $question->fresh()->status);
        $this->assertDatabaseHas('content_review_logs', [
            'reviewable_id' => $question->id,
            'action' => 'SUBMITTED_FOR_REVIEW',
        ]);

        // 3. Theological Reviewer Approves and Publishes
        $pipeline->approveContent(
            $question->fresh(),
            $this->reviewer,
            true,
            5,
            5,
            'Doctrinally sound in accordance with CCC.'
        );

        $question->refresh();
        $this->assertEquals('PUBLISHED', $question->status);
        $this->assertEquals($this->reviewer->id, $question->theological_reviewer_id);
        $this->assertNotNull($question->published_at);
        $this->assertDatabaseHas('content_review_logs', [
            'reviewable_id' => $question->id,
            'action' => 'PUBLISHED',
            'theological_accuracy_rating' => 5,
        ]);
    }

    public function test_unapproved_content_is_never_visible_in_published_queries(): void
    {
        $track = TaxonomyTrack::first();
        $questionService = app(QuestionBankService::class);

        $draftQ = $questionService->createQuestion([
            'track_id' => $track->id,
            'question_text' => 'Draft unapproved question',
            'status' => 'AI_GENERATED',
        ], [
            ['option_key' => 'A', 'option_text' => 'Option 1', 'is_correct' => true],
        ]);

        $publishedQ = $questionService->createQuestion([
            'track_id' => $track->id,
            'question_text' => 'Officially approved question',
            'status' => 'PUBLISHED',
        ], [
            ['option_key' => 'A', 'option_text' => 'Option 1', 'is_correct' => true],
        ]);

        $youthVisible = QuestionBankItem::where('status', 'PUBLISHED')->get();
        $this->assertTrue($youthVisible->contains('id', $publishedQ->id));
        $this->assertFalse($youthVisible->contains('id', $draftQ->id));
    }
}
