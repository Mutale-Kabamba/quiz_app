<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Deanery;
use App\Models\DiocesanCompetition;
use App\Models\Parish;
use App\Models\ParishTransfer;
use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\QuizAttemptAnswer;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\XpTransaction;
use App\Services\AuditLogService;
use App\Services\DiocesanAnalyticsService;
use App\Services\QuestionQualityService;
use App\Services\XpLedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $chairperson;
    protected Deanery $deanery;
    protected Parish $parishA;
    protected Parish $parishB;
    protected User $youth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Diocesan Super Admin',
            'phone' => '+260970000001',
            'email' => 'superadmin@diocese.org',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'approved',
        ]);

        $this->deanery = Deanery::create(['code' => 'LIV', 'name' => 'Livingstone Deanery']);
        $this->parishA = Parish::create(['deanery_id' => $this->deanery->id, 'name' => "St. Theresa's Cathedral", 'location' => 'Livingstone']);
        $this->parishB = Parish::create(['deanery_id' => $this->deanery->id, 'name' => 'Holy Cross Parish', 'location' => 'Maramba']);

        $this->chairperson = User::create([
            'parish_id' => $this->parishA->id,
            'name' => 'Chairperson Theresa',
            'phone' => '+260970000002',
            'email' => 'chairperson@theresa.org',
            'password' => bcrypt('password'),
            'role' => 'chairperson',
            'status' => 'approved',
        ]);

        $this->youth = User::create([
            'parish_id' => $this->parishA->id,
            'name' => 'Mubita Mubita',
            'phone' => '+260970000003',
            'email' => 'mubita@example.com',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 400,
            'level' => 3,
            'current_streak' => 5,
        ]);
    }

    public function test_super_admin_can_create_deanery_and_parish(): void
    {
        $this->actingAs($this->superAdmin);

        $newDeanery = Deanery::create([
            'code' => 'KAL',
            'name' => 'Kalomo Deanery',
            'description' => 'Kalomo and Zimba pastoral districts',
        ]);

        $newParish = Parish::create([
            'deanery_id' => $newDeanery->id,
            'name' => 'St. Joseph Kalomo',
            'location' => 'Kalomo Town',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('deaneries', ['code' => 'KAL']);
        $this->assertDatabaseHas('parishes', ['name' => 'St. Joseph Kalomo']);
    }

    public function test_xp_ledger_awards_authoritative_xp_and_calculates_level(): void
    {
        $this->youth->update(['xp' => 0, 'level' => 1]);
        $ledgerService = app(XpLedgerService::class);

        // Award +50 XP for daily challenge
        $txn = $ledgerService->awardXp(
            $this->youth,
            50,
            'daily_challenge',
            'challenge-1',
            'Completed Daily Scripture Challenge'
        );

        $this->assertInstanceOf(XpTransaction::class, $txn);
        $this->assertEquals(50, $txn->amount);
        $this->assertEquals(50, $this->youth->fresh()->xp);
        $this->assertEquals(1, $this->youth->fresh()->level); // sqrt(50/100) + 1 = 1

        // Award +400 XP -> Total 450 XP -> Level = floor(sqrt(4.5)) + 1 = 3
        $ledgerService->awardXp($this->youth, 400, 'ranked_quiz', 'quiz-1', 'Ranked Quiz Victory');
        $this->assertEquals(450, $this->youth->fresh()->xp);
        $this->assertEquals(3, $this->youth->fresh()->level);
    }

    public function test_super_admin_can_correct_user_score_with_audit(): void
    {
        $ledgerService = app(XpLedgerService::class);

        $ledgerService->correctXp(
            $this->superAdmin,
            $this->youth,
            100,
            'Correction of glitch in competition rally #4'
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'xp_score_corrected',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_youth_parish_transfer_preserves_progress_and_creates_audit_log(): void
    {
        $transfer = ParishTransfer::create([
            'user_id' => $this->youth->id,
            'from_parish_id' => $this->parishA->id,
            'to_parish_id' => $this->parishB->id,
            'requested_by' => $this->chairperson->id,
            'reason' => 'Family relocation to Maramba',
            'status' => 'pending',
        ]);

        // Super Admin approves transfer
        $this->actingAs($this->superAdmin);
        $this->youth->update(['parish_id' => $transfer->to_parish_id]);
        $transfer->update([
            'status' => 'approved',
            'reviewed_by' => $this->superAdmin->id,
            'reviewed_at' => now(),
        ]);

        app(AuditLogService::class)->log(
            'transfer_request_approved',
            $transfer,
            ['parish_id' => $this->parishA->id],
            ['parish_id' => $this->parishB->id, 'user_id' => $this->youth->id]
        );

        // Verify Youth is in Parish B but XP and streak are untouched
        $freshYouth = $this->youth->fresh();
        $this->assertEquals($this->parishB->id, $freshYouth->parish_id);
        $this->assertEquals(400, $freshYouth->xp);
        $this->assertEquals(5, $freshYouth->current_streak);
        $this->assertDatabaseHas('audit_logs', ['action' => 'transfer_request_approved']);
    }

    public function test_diocesan_analytics_computes_kpis(): void
    {
        $analyticsService = app(DiocesanAnalyticsService::class);
        $kpis = $analyticsService->getDiocesanKpis();

        $this->assertEquals(1, $kpis['total_youth']);
        $this->assertEquals(2, $kpis['total_parishes']);
        $this->assertArrayHasKey('dau', $kpis);
        $this->assertArrayHasKey('wau', $kpis);
    }

    public function test_question_quality_service_identifies_flagged_issues(): void
    {
        $category = Category::create([
            'name' => 'Sacraments & Liturgy',
            'slug' => 'sacraments-liturgy',
        ]);

        $question = Question::create([
            'category_id' => $category->id,
            'question_text' => 'How many sacraments are in the Catholic Church?',
            'options' => ['A' => '5', 'B' => '7', 'C' => '10', 'D' => '12'],
            'correct_option_key' => 'B',
            'level' => 1,
            'is_active' => true,
        ]);

        // Submit a dispute report
        QuestionReport::create([
            'user_id' => $this->chairperson->id,
            'question_id' => $question->id,
            'issue_type' => 'bad_reference',
            'notes' => 'Typo in catechism citation CCC 1210',
            'status' => 'pending',
        ]);

        $quality = app(QuestionQualityService::class)->getQuestionQuality($question);
        $this->assertEquals('danger', $quality['status']);
        $this->assertEquals(1, $quality['report_count']);
    }

    public function test_super_admin_can_schedule_and_activate_diocesan_competition(): void
    {
        $competition = DiocesanCompetition::create([
            'created_by' => $this->superAdmin->id,
            'title' => 'Diocesan Youth Bible Rally 2026',
            'competition_type' => 'diocesan',
            'rally_pin' => '789123',
            'level' => 2,
            'time_limit_seconds' => 15,
            'question_count' => 20,
            'status' => 'scheduled',
        ]);

        $this->assertEquals('scheduled', $competition->status);

        // Go Live
        $competition->update(['status' => 'live']);
        $this->assertEquals('live', $competition->fresh()->status);
    }

    public function test_system_settings_stores_and_retrieves_configuration(): void
    {
        SystemSetting::set('diocese_name', 'Catholic Diocese of Livingstone', 'general');
        SystemSetting::set('xp_challenge', 75, 'gamification');

        $this->assertEquals('Catholic Diocese of Livingstone', SystemSetting::get('diocese_name'));
        $this->assertEquals(75, SystemSetting::get('xp_challenge'));
    }
}
