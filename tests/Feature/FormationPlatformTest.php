<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DailyChallenge;
use App\Models\Deanery;
use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\Parish;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\FlashcardService;
use App\Services\GamificationService;
use App\Services\LeaderboardService;
use App\Services\LearningIntelligenceService;
use App\Services\LearningProgressService;
use App\Services\StreakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormationPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Lesson $lesson;
    protected Flashcard $flashcard;
    protected Question $question;

    protected function setUp(): void
    {
        parent::setUp();

        $deanery = Deanery::create(['code' => 'LIV', 'name' => 'Livingstone Deanery']);
        $parish = Parish::create(['deanery_id' => $deanery->id, 'name' => "St. Theresa's Cathedral", 'location' => 'Livingstone']);

        $this->user = User::create([
            'parish_id' => $parish->id,
            'name' => 'Mutale Mwamba',
            'email' => 'mutale@example.com',
            'phone' => '+260970000001',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 100,
            'level' => 2,
            'current_streak' => 2,
            'longest_streak' => 5,
        ]);

        $this->category = Category::create([
            'name' => 'Holy Scripture',
            'slug' => 'holy-scripture',
            'description' => 'Biblical Catechesis',
            'icon' => 'heroicon-o-book-open',
            'display_order' => 1,
        ]);

        $this->lesson = Lesson::create([
            'category_id' => $this->category->id,
            'title' => 'The Holy Gospel According to St. Mark',
            'slug' => 'st-mark-gospel',
            'subheading' => 'The Servant Messiah and Son of God',
            'summary_takeaways' => ['Written by John Mark', 'Focuses on Christ the Servant'],
            'content_sections' => [
                ['heading' => 'Introduction', 'body' => 'Mark writes for Roman believers.', 'scripture_quote' => 'Mark 1:1']
            ],
            'key_terms' => [['term' => 'Messianic Secret', 'definition' => 'Jesus motif in Mark']],
            'estimated_read_minutes' => 4,
            'difficulty' => 1,
            'display_order' => 1,
            'status' => 'published',
        ]);

        $this->flashcard = Flashcard::create([
            'category_id' => $this->category->id,
            'lesson_id' => $this->lesson->id,
            'front_text' => 'Who authored the earliest Gospel?',
            'back_text' => 'St. Mark (John Mark).',
            'reference_citation' => 'Scripture / Mark 1:1',
            'difficulty' => 1,
            'status' => 'published',
        ]);

        $this->question = Question::create([
            'category_id' => $this->category->id,
            'level' => 1,
            'question_text' => 'How many chapters are in the Gospel of Mark?',
            'options' => ['A' => '16', 'B' => '28', 'C' => '24', 'D' => '21'],
            'correct_option_key' => 'A',
            'explanation' => 'Mark is the shortest gospel with 16 chapters.',
            'reference_citation' => 'Gospel of Mark',
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');

        $studyResponse = $this->get('/study');
        $studyResponse->assertRedirect('/login');

        $quizResponse = $this->get('/quiz');
        $quizResponse->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_personalized_home_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Mutale');
        $response->assertSee('Level 2');
        $response->assertSee('Holy Scripture');
    }

    public function test_arena_hub_renders_practice_and_compete_options_without_auto_starting(): void
    {
        // 1. Practice Tab (Default)
        $response = $this->actingAs($this->user)->get('/quiz');
        $response->assertStatus(200);
        $response->assertSee('Quiz &amp; Competition Arena', false);
        $response->assertSee('Practice');
        $response->assertSee('Compete');
        $response->assertSee('Available Practice Quizzes');
        $response->assertSee('Holy Scripture');

        // 2. Compete Tab
        $competeResponse = $this->actingAs($this->user)->get('/quiz?tab=compete');
        $competeResponse->assertStatus(200);
        $competeResponse->assertSee('Livingstone Diocesan Ranked Arena');
        $competeResponse->assertSee('Live Youth Rally Lobby');
        $competeResponse->assertSee('Upcoming Deanery Rallies');
    }

    public function test_user_can_launch_interactive_quiz_session(): void
    {
        $response = $this->actingAs($this->user)->get("/quiz/play/{$this->category->id}?mode=practice");
        $response->assertStatus(200);
        $response->assertSee('Question Progress');
        $response->assertSee('How many chapters are in the Gospel of Mark?');
    }

    public function test_user_can_view_and_complete_structured_lesson(): void
    {
        $response = $this->actingAs($this->user)->get("/lesson/{$this->lesson->id}");
        $response->assertStatus(200);
        $response->assertSee('The Holy Gospel According to St. Mark');
        $response->assertSee('Key Catechetical Takeaways');
        $response->assertSee('Messianic Secret');

        // Service completion test
        $progressService = app(LearningProgressService::class);
        $result = $progressService->completeLesson($this->user, $this->lesson);

        $this->assertTrue($result['first_time']);
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->user->id,
            'lesson_id' => $this->lesson->id,
            'is_completed' => true,
        ]);
    }

    public function test_flashcard_spaced_repetition_review(): void
    {
        $response = $this->actingAs($this->user)->get('/flashcards');
        $response->assertStatus(200);
        $response->assertSee('Who authored the earliest Gospel?');

        $flashcardService = app(FlashcardService::class);
        $review = $flashcardService->recordReview($this->user, $this->flashcard->id, 3); // Easy (7 days)

        $this->assertEquals(3, $review->rating);
        $this->assertDatabaseHas('flashcard_reviews', [
            'user_id' => $this->user->id,
            'flashcard_id' => $this->flashcard->id,
            'rating' => 3,
        ]);
    }

    public function test_gamification_service_awards_xp_and_advances_levels(): void
    {
        $gamification = app(GamificationService::class);
        $initialXp = $this->user->xp;

        // Award 400 XP (Should level up to Level 3)
        $result = $gamification->awardXp($this->user, 400, "Tested Formation Challenge");

        $this->assertEquals(400, $result['xp_gained']);
        $this->assertEquals($initialXp + 400, $result['total_xp']);
        $this->assertTrue($result['leveled_up']);
        $this->assertEquals(3, $result['new_level']);
    }

    public function test_streak_service_updates_formation_streak(): void
    {
        $streakService = app(StreakService::class);
        $result = $streakService->recordFormationActivity($this->user);

        $this->assertGreaterThanOrEqual(1, $result['current_streak']);
        $this->assertNotNull($this->user->fresh()->last_activity_date);
    }

    public function test_leaderboard_service_calculates_rankings(): void
    {
        QuizAttempt::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'level' => 1,
            'mode' => 'ranked',
            'score' => 450,
            'total_questions' => 5,
            'correct_answers_count' => 4,
            'time_taken_seconds' => 45,
            'completed_at' => now(),
            'is_synced' => true,
        ]);

        $leaderboard = app(LeaderboardService::class)->getRankings('diocese', 'this_week', null, $this->user);

        $this->assertNotEmpty($leaderboard['top3']);
        $this->assertEquals(1, $leaderboard['userRank']);
        $this->assertEquals(450, $leaderboard['userPoints']);
    }
}
