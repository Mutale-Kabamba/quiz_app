<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Deanery;
use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\MicroLesson;
use App\Models\Parish;
use App\Models\ParishFormationChallenge;
use App\Models\Question;
use App\Models\RallyPreparation;
use App\Models\TaxonomyCategory;
use App\Models\TaxonomyConcept;
use App\Models\TaxonomyDomain;
use App\Models\TaxonomyTopic;
use App\Models\TaxonomyTrack;
use App\Models\User;
use App\Models\UserTopicMastery;
use App\Services\AdaptiveMasteryService;
use App\Services\CatholicCitationService;
use App\Services\CatholicDoctrinalExplanationService;
use App\Services\KnowledgeTaxonomyService;
use App\Services\MicroLearningService;
use App\Services\ParishCommunityChallengeService;
use App\Services\RallyPreparationService;
use App\Services\SpacedReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DigitalFormationPlatformEcosystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Parish $parish;
    protected TaxonomyTopic $topic;
    protected TaxonomyConcept $concept;

    protected function setUp(): void
    {
        parent::setUp();

        $deanery = Deanery::create(['code' => 'LIV', 'name' => 'Livingstone Deanery']);
        $this->parish = Parish::create(['deanery_id' => $deanery->id, 'name' => "St. Theresa's Cathedral", 'location' => 'Livingstone']);

        $this->user = User::create([
            'parish_id' => $this->parish->id,
            'name' => 'Mutale Mwamba',
            'email' => 'mutale@example.com',
            'phone' => '+260970000001',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 120,
            'level' => 2,
            'current_streak' => 3,
        ]);

        $track = TaxonomyTrack::create(['name' => 'Sacramental Theology', 'slug' => 'sacramental-theology', 'code' => 'SAC']);
        $domain = TaxonomyDomain::create(['track_id' => $track->id, 'name' => 'Sacraments of Initiation', 'slug' => 'sacraments-initiation']);
        $cat = TaxonomyCategory::create(['track_id' => $track->id, 'domain_id' => $domain->id, 'name' => 'The Holy Eucharist', 'slug' => 'holy-eucharist']);
        $this->topic = TaxonomyTopic::create(['category_id' => $cat->id, 'name' => 'Real Presence & Transubstantiation', 'slug' => 'real-presence']);
        $this->concept = TaxonomyConcept::create([
            'topic_id' => $this->topic->id,
            'name' => 'Transubstantiation',
            'slug' => 'transubstantiation',
            'definition' => 'The conversion of the substance of bread and wine into the body and blood of Christ.',
            'ccc_reference' => 'CCC 1376',
            'scripture_reference' => '1 Corinthians 11:23-26',
        ]);
    }

    public function test_micro_learning_service_completes_5_minute_formation(): void
    {
        $service = app(MicroLearningService::class);
        $lesson = $service->getTodayMicroLesson($this->user);

        $this->assertNotNull($lesson);
        $this->assertEquals(4, $lesson->read_time_minutes);

        $completion = $service->completeMicroLesson($this->user, $lesson, 3, 3);

        $this->assertNotNull($completion);
        $this->assertTrue($service->hasUserCompleted($this->user, $lesson));
        $this->assertDatabaseHas('user_micro_lesson_completions', [
            'user_id' => $this->user->id,
            'micro_lesson_id' => $lesson->id,
            'quiz_score' => 3,
        ]);
    }

    public function test_spaced_review_service_schedules_mistakes_and_advances_intervals(): void
    {
        $service = app(SpacedReviewService::class);
        $questionId = 'q-101-uuid';

        // 1. Record a mistake -> schedules review for tomorrow (1-day interval)
        $review = $service->recordMistake($this->user, $questionId, $this->concept->id, $this->topic->id);
        $this->assertEquals(1, $review->interval_days);
        $this->assertEquals(1, $review->mistake_count);
        $this->assertFalse($review->is_mastered);

        // 2. Record success -> moves to next interval (3 days)
        $updatedReview = $service->recordSuccess($this->user, $questionId);
        $this->assertEquals(3, $updatedReview->interval_days);
        $this->assertEquals(1, $updatedReview->consecutive_correct);
    }

    public function test_rally_preparation_service_calculates_readiness_and_records_training(): void
    {
        $service = app(RallyPreparationService::class);
        $rally = $service->getActiveRally();

        $this->assertNotNull($rally);
        $this->assertStringContainsString('Livingstone Diocesan Youth Rally', $rally->title);

        $readiness = $service->calculateReadiness($this->user, $rally);
        $this->assertGreaterThanOrEqual(20, $readiness->scripture_readiness);
        $this->assertGreaterThanOrEqual(20, $readiness->catechism_readiness);

        // Record a training drill
        $afterTraining = $service->recordTrainingDrill($this->user, $rally, 10, 8);
        $this->assertEquals(10, $afterTraining->training_questions_answered);
    }

    public function test_parish_community_challenge_tracks_collective_contributions(): void
    {
        $service = app(ParishCommunityChallengeService::class);
        $challenges = $service->getActiveChallengesForParish($this->parish);

        $this->assertNotEmpty($challenges);
        $challenge = $challenges->first();

        // Record youth contribution
        $service->recordContribution($this->user, 150, 2);

        $standings = $service->getChallengeStandings($challenge);
        $this->assertEquals(150, $standings['parish_1']['total_xp']);
        $this->assertEquals(1, $standings['parish_1']['youth_count']);
    }

    public function test_catholic_doctrinal_explanation_service_provides_structured_explanations(): void
    {
        $service = app(CatholicDoctrinalExplanationService::class);
        $explanation = $service->getExplanation($this->concept);

        $this->assertEquals('Transubstantiation', $explanation['concept_title']);
        $this->assertStringContainsString('CCC 1376', $explanation['catechism_citation']);
        $this->assertNotEmpty($explanation['simple_explanation']);
        $this->assertNotEmpty($explanation['doctrinal_explanation']);
    }

    public function test_mobile_dashboard_renders_all_digital_formation_engines(): void
    {
        $response = $this->actingAs($this->user)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Peace be with you, Mutale');
        $response->assertSee("Today's Formation", false);
        $response->assertSee('Prepare for the Rally');
        $response->assertSee('Parish Formation Challenge');
    }
}
