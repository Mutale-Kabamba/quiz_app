<?php

namespace Tests\Feature;

use App\Models\FormationLevel;
use App\Models\QuestionBankItem;
use App\Models\QuizBlueprint;
use App\Models\TaxonomyTopic;
use App\Models\TaxonomyTrack;
use App\Models\User;
use App\Models\UserTopicMastery;
use App\Services\AdaptiveMasteryService;
use App\Services\QuestionBankService;
use App\Services\SmartQuizEngine;
use App\Services\StudyRecommendationEngine;
use Database\Seeders\CatholicKnowledgeTaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdaptiveLearningAndSmartQuizTest extends TestCase
{
    use RefreshDatabase;

    protected User $youth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatholicKnowledgeTaxonomySeeder::class);

        $this->youth = User::create([
            'name' => 'Chanda Musonda',
            'phone' => '+260970000012',
            'email' => 'chanda@youth.org',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 120,
            'level' => 2,
        ]);
    }

    public function test_adaptive_mastery_service_tracks_topic_accuracy_and_confidence(): void
    {
        $topic = TaxonomyTopic::first();
        $service = app(AdaptiveMasteryService::class);

        // Youth answers 10 questions: 8 correct, 2 incorrect
        $mastery = $service->recordTopicActivity($this->youth, $topic->id, 10, 8);

        $this->assertInstanceOf(UserTopicMastery::class, $mastery);
        $this->assertEquals(10, $mastery->questions_attempted);
        $this->assertEquals(8, $mastery->questions_correct);
        $this->assertEquals('MEDIUM', $mastery->confidence_level);
        $this->assertGreaterThan(0, $mastery->mastery_score);
    }

    public function test_smart_quiz_engine_generates_balanced_quiz_from_blueprint(): void
    {
        $track = TaxonomyTrack::first();
        $topic = TaxonomyTopic::first();
        $qService = app(QuestionBankService::class);

        // Seed 10 published questions
        for ($i = 1; $i <= 10; $i++) {
            $qService->createQuestion([
                'track_id' => $track->id,
                'topic_id' => $topic->id,
                'question_text' => "Sample Catholic Question #{$i} for Blueprint",
                'status' => 'PUBLISHED',
                'is_practice_eligible' => true,
            ], [
                ['option_key' => 'A', 'option_text' => 'Option A', 'is_correct' => true],
                ['option_key' => 'B', 'option_text' => 'Option B', 'is_correct' => false],
            ]);
        }

        $blueprint = QuizBlueprint::create([
            'title' => 'Sunday Formation 5-Question Quiz',
            'slug' => 'sunday-5-question-quiz',
            'question_count' => 5,
            'unseen_question_ratio' => 80,
            'is_active' => true,
        ]);

        $quizEngine = app(SmartQuizEngine::class);
        $selectedQuestions = $quizEngine->generateQuiz($blueprint, $this->youth, $topic->id);

        $this->assertEquals(5, $selectedQuestions->count());
        $this->assertTrue($selectedQuestions->every(fn($q) => $q->status === 'PUBLISHED'));
    }

    public function test_study_recommendation_engine_builds_personalized_feed(): void
    {
        $feed = app(StudyRecommendationEngine::class)->getPersonalizedFeed($this->youth);

        $this->assertArrayHasKey('saint_of_the_day', $feed);
        $this->assertArrayHasKey('weak_areas', $feed);
        $this->assertArrayHasKey('quick_reads', $feed);
        $this->assertArrayHasKey('featured_study', $feed);
    }
}
