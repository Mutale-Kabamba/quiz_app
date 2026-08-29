<?php

namespace Tests\Feature;

use App\Models\Deanery;
use App\Models\Parish;
use App\Models\ParishAnnouncement;
use App\Models\ParishEvent;
use App\Models\ParishTransfer;
use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\User;
use App\Services\ParishAnalyticsService;
use App\Services\ParishDashboardService;
use App\Services\ParishReportService;
use App\Services\ParishYouthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParishAdminExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected Parish $parishA;
    protected Parish $parishB;
    protected User $adminA;
    protected User $adminB;
    protected User $youthA;
    protected User $youthB;

    protected function setUp(): void
    {
        parent::setUp();

        $deanery = Deanery::create(['code' => 'LIV', 'name' => 'Livingstone Deanery']);
        $this->parishA = Parish::create(['deanery_id' => $deanery->id, 'name' => "St. Theresa's Cathedral", 'location' => 'Livingstone']);
        $this->parishB = Parish::create(['deanery_id' => $deanery->id, 'name' => 'Holy Cross Parish', 'location' => 'Maramba']);

        $this->adminA = User::create([
            'parish_id' => $this->parishA->id,
            'name' => 'Chairperson Theresa',
            'phone' => '+260970000010',
            'email' => 'adminA@example.com',
            'password' => bcrypt('password'),
            'role' => 'chairperson',
            'status' => 'approved',
        ]);

        $this->adminB = User::create([
            'parish_id' => $this->parishB->id,
            'name' => 'Chairperson Holy Cross',
            'phone' => '+260970000020',
            'email' => 'adminB@example.com',
            'password' => bcrypt('password'),
            'role' => 'chairperson',
            'status' => 'approved',
        ]);

        $this->youthA = User::create([
            'parish_id' => $this->parishA->id,
            'name' => 'Mutale Mwamba',
            'phone' => '+260970000011',
            'email' => 'mutale@example.com',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 350,
            'level' => 2,
            'last_activity_date' => now(),
        ]);

        $this->youthB = User::create([
            'parish_id' => $this->parishB->id,
            'name' => 'Chileshe Bwalya',
            'phone' => '+260970000021',
            'email' => 'chileshe@example.com',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 150,
            'level' => 1,
            'last_activity_date' => now(),
        ]);
    }

    public function test_parish_admin_cannot_access_or_suspend_youth_from_another_parish(): void
    {
        $youthService = app(ParishYouthService::class);

        // Admin A tries to suspend Youth B (from Parish B) -> Throws AuthorizationException
        $this->expectException(AuthorizationException::class);
        $youthService->suspendYouth($this->adminA, $this->youthB, 'Unauthorized attempt');
    }

    public function test_parish_admin_can_suspend_and_reactivate_own_parish_youth(): void
    {
        $youthService = app(ParishYouthService::class);

        // Suspend Youth A
        $youthService->suspendYouth($this->adminA, $this->youthA, 'Inappropriate conduct');
        $this->assertEquals('rejected', $this->youthA->fresh()->status);
        $this->assertEquals('Inappropriate conduct', $this->youthA->fresh()->rejection_reason);

        // Reactivate Youth A
        $youthService->reactivateYouth($this->adminA, $this->youthA);
        $this->assertEquals('approved', $this->youthA->fresh()->status);
        $this->assertNull($this->youthA->fresh()->rejection_reason);
    }

    public function test_parish_admin_adds_youth_with_server_enforced_parish(): void
    {
        $youthService = app(ParishYouthService::class);

        $newYouth = $youthService->createYouth($this->adminA, [
            'name' => 'Thandiwe Phiri',
            'phone' => '+260970000099',
            'email' => 'thandiwe@example.com',
            'password' => 'secret123',
        ]);

        $this->assertEquals($this->parishA->id, $newYouth->parish_id);
        $this->assertEquals('youth', $newYouth->role);
        $this->assertEquals('approved', $newYouth->status);
        $this->assertEquals($this->adminA->id, $newYouth->approved_by);
    }

    public function test_parish_admin_can_request_youth_transfer(): void
    {
        $youthService = app(ParishYouthService::class);

        $transfer = $youthService->requestTransfer(
            $this->adminA,
            $this->youthA,
            $this->parishB->id,
            'Relocated to Maramba area for university'
        );

        $this->assertInstanceOf(ParishTransfer::class, $transfer);
        $this->assertEquals('pending', $transfer->status);
        $this->assertEquals($this->parishA->id, $transfer->from_parish_id);
        $this->assertEquals($this->parishB->id, $transfer->to_parish_id);
    }

    public function test_parish_dashboard_service_computes_kpis_and_health(): void
    {
        $dashboardService = app(ParishDashboardService::class);
        $kpis = $dashboardService->getParishKpis($this->parishA->id);

        $this->assertEquals(1, $kpis['total_youth']);
        $this->assertEquals(1, $kpis['active_this_week']);
        $this->assertArrayHasKey('health_status', $kpis);
        $this->assertEquals('🟢 Strong', $kpis['health_status']['badge']);
    }

    public function test_parish_monthly_report_service_generates_accurate_summary(): void
    {
        $reportService = app(ParishReportService::class);
        $report = $reportService->generateMonthlyReport($this->parishA->id);

        $this->assertEquals("St. Theresa's Cathedral", $report['parish_name']);
        $this->assertEquals(1, $report['total_youth']);
        $this->assertEquals('Mutale Mwamba', $report['top_youth_name']);
    }

    public function test_parish_events_and_announcements_created_under_parish(): void
    {
        $event = ParishEvent::create([
            'parish_id' => $this->parishA->id,
            'created_by' => $this->adminA->id,
            'title' => 'Sunday Youth Catechism Practice',
            'event_type' => 'quiz_practice',
            'event_date' => now()->addDays(2),
            'location' => 'Parish Hall',
            'status' => 'published',
        ]);

        $announcement = ParishAnnouncement::create([
            'parish_id' => $this->parishA->id,
            'created_by' => $this->adminA->id,
            'title' => 'Youth Meeting Time Change',
            'message' => 'Please note that tomorrow meeting will start at 15:00.',
            'target_type' => 'all',
            'priority' => 'normal',
            'sent_at' => now(),
        ]);

        $this->assertEquals($this->parishA->id, $event->parish_id);
        $this->assertEquals($this->parishA->id, $announcement->parish_id);
    }
}
