<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\Deanery;
use App\Models\Lesson;
use App\Models\Parish;
use App\Models\ParishTransfer;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\XpTransaction;
use App\Services\DiocesanAnalyticsService;
use App\Services\GamificationService;
use App\Services\LeaderboardService;
use App\Services\LearningProgressService;
use App\Services\ParishDashboardService;
use App\Services\ParishYouthService;
use App\Services\XpLedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnifiedPlatformIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_unified_platform_chain_from_super_admin_to_youth_to_analytics(): void
    {
        // 1. Super Admin sets up Diocesan hierarchy
        $superAdmin = User::create([
            'name' => 'Diocesan Bishop / Super Admin',
            'phone' => '+260970000001',
            'email' => 'bishop@livingstonediocese.org',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'approved',
        ]);

        $deanery = Deanery::create([
            'code' => 'LIV',
            'name' => 'Livingstone Deanery',
            'description' => 'Livingstone Urban pastoral district',
        ]);

        $parishA = Parish::create([
            'deanery_id' => $deanery->id,
            'name' => "St. Theresa's Cathedral",
            'location' => 'Livingstone Town Center',
            'is_active' => true,
        ]);

        $parishB = Parish::create([
            'deanery_id' => $deanery->id,
            'name' => 'Holy Cross Parish',
            'location' => 'Maramba',
            'is_active' => true,
        ]);

        // 2. Super Admin creates Parish Admin for St. Theresa's
        $chairpersonA = User::create([
            'parish_id' => $parishA->id,
            'name' => 'Theresa Parish Chairperson',
            'phone' => '+260970000002',
            'email' => 'chairperson@theresa.org',
            'password' => bcrypt('password'),
            'role' => 'chairperson',
            'status' => 'approved',
        ]);

        // 3. Parish Admin adds Youth Member (server enforces parish_id)
        $youth = app(ParishYouthService::class)->createYouth($chairpersonA, [
            'name' => 'Mutale Mwamba',
            'phone' => '+260970000003',
            'email' => 'mutale@example.com',
            'password' => 'secret123',
        ]);

        $this->assertEquals($parishA->id, $youth->parish_id);
        $this->assertEquals('youth', $youth->role);

        // 4. Super Admin publishes Catholic Formation Track & Lesson
        $category = Category::create([
            'name' => 'Holy Scripture',
            'slug' => 'holy-scripture',
            'icon' => '📜',
        ]);

        $lesson = Lesson::create([
            'category_id' => $category->id,
            'title' => 'The Gospel According to St. Mark',
            'slug' => 'the-gospel-according-to-st-mark',
            'subtitle' => 'The Proclamation of the Kingdom',
            'reading_time_minutes' => 7,
            'content_sections' => [
                ['heading' => 'Context', 'body' => 'Mark presents Jesus as the Suffering Servant.'],
            ],
            'key_takeaways' => ['Mark is the earliest Gospel', 'Focuses on action and discipleship'],
            'scripture_references' => ['Mark 1:1-15'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Milestone Achievement definition
        $achievement = Achievement::create([
            'code' => 'first_lesson',
            'title' => 'Scripture Explorer',
            'description' => 'Complete your first Holy Scripture lesson',
            'icon' => '📜',
            'type' => 'lesson_count',
            'threshold' => 1,
            'xp_reward' => 50,
        ]);

        // 5. Youth studies & completes Lesson
        $this->actingAs($youth);
        $progressResult = app(LearningProgressService::class)->completeLesson($youth, $lesson);

        $this->assertTrue($progressResult['first_time']);
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $youth->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true,
        ]);

        // Verify Authoritative XP Ledger transaction created
        $this->assertDatabaseHas('xp_transactions', [
            'user_id' => $youth->id,
            'source_type' => 'lesson_completed',
            'source_id' => (string) $lesson->id,
            'amount' => 20,
        ]);

        // Verify Achievement unlocked automatically with bonus XP
        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $youth->id,
            'achievement_id' => $achievement->id,
        ]);

        $this->assertDatabaseHas('xp_transactions', [
            'user_id' => $youth->id,
            'source_type' => 'achievement_unlocked',
            'source_id' => (string) $achievement->id,
            'amount' => 50,
        ]);

        // Total XP = 20 (lesson) + 50 (badge) = 70 XP
        $youth->refresh();
        $this->assertEquals(70, $youth->xp);

        // 6. Youth takes and completes a Ranked Quiz
        $quizAttempt = QuizAttempt::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $youth->id,
            'category_id' => $category->id,
            'level' => 1,
            'mode' => 'ranked',
            'score' => 250,
            'total_questions' => 10,
            'correct_answers_count' => 9,
            'time_taken_seconds' => 45,
            'completed_at' => now(),
        ]);

        app(GamificationService::class)->awardXp($youth, 25, "Completed Ranked Quiz", 'quiz_attempt', (string) $quizAttempt->id);
        $youth->refresh();
        $this->assertEquals(95, $youth->xp); // 70 + 25 = 95 XP

        // 7. Verify Parish Dashboard KPIs update automatically from real database activity
        $parishKpis = app(ParishDashboardService::class)->getParishKpis($parishA->id);
        $this->assertEquals(1, $parishKpis['total_youth']);
        $this->assertEquals(1, $parishKpis['active_this_week']);
        $this->assertEquals(1, $parishKpis['lessons_completed']);
        $this->assertEquals(1, $parishKpis['quizzes_completed']);
        $this->assertEquals(95, $parishKpis['parish_xp']);
        $this->assertEquals('🟢 Strong', $parishKpis['health_status']['badge']);

        // 8. Verify Parish Leaderboard reflects the single source of truth
        $leaderboard = app(LeaderboardService::class)->getRankings('parish', 'this_week', null, $youth);
        $topYouth = $leaderboard['top3']->first();
        $this->assertNotNull($topYouth);
        $this->assertEquals('Mutale Mwamba', $topYouth->user_name);
        $this->assertEquals(250, $topYouth->total_points);

        // 9. Verify Deanery Analytics aggregate the Parish
        $deaneryAnalytics = app(DiocesanAnalyticsService::class)->getDeaneryPerformance();
        $this->assertNotEmpty($deaneryAnalytics);
        $livingstoneDeanery = collect($deaneryAnalytics)->firstWhere('code', 'LIV');
        $this->assertEquals(1, $livingstoneDeanery['total_youth']);
        $this->assertEquals(1, $livingstoneDeanery['active_youth']);
        $this->assertEquals(95, $livingstoneDeanery['total_xp']);

        // 10. Verify Diocesan Super Admin Command Dashboard reflects the exact same truth
        $diocesanKpis = app(DiocesanAnalyticsService::class)->getDiocesanKpis();
        $this->assertEquals(1, $diocesanKpis['total_youth']);
        $this->assertEquals(1, $diocesanKpis['active_this_week']);
        $this->assertEquals(1, $diocesanKpis['lessons_completed']);
        $this->assertEquals(1, $diocesanKpis['quizzes_completed']);
        $this->assertEquals(95, $diocesanKpis['total_xp_awarded']);
        $this->assertEquals(1, $diocesanKpis['active_parishes']);

        // 11. Youth Parish Transfer: Move to Holy Cross without destroying learning history
        $transfer = app(ParishYouthService::class)->requestTransfer(
            $chairpersonA,
            $youth,
            $parishB->id,
            'Relocated to Maramba parish'
        );

        $this->assertEquals('pending', $transfer->status);

        // Super Admin approves transfer
        $this->actingAs($superAdmin);
        $youth->update(['parish_id' => $parishB->id]);
        $transfer->update(['status' => 'approved', 'reviewed_by' => $superAdmin->id, 'reviewed_at' => now()]);

        $youth->refresh();
        $this->assertEquals($parishB->id, $youth->parish_id);
        $this->assertEquals(95, $youth->xp); // XP is 100% preserved
        $this->assertEquals(1, $youth->lessonProgress()->where('is_completed', true)->count()); // Learning is preserved
        $this->assertEquals(1, $youth->quizAttempts()->count()); // Quizzes are preserved
        $this->assertEquals(1, $youth->achievements()->count()); // Badges are preserved
    }
}
