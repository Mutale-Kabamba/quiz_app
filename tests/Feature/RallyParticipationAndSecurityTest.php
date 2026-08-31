<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Deanery;
use App\Models\DiocesanCompetition;
use App\Models\Parish;
use App\Models\Question;
use App\Models\RallyJoinRequest;
use App\Models\RallyParticipant;
use App\Models\User;
use App\Services\RallyAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RallyParticipationAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected Deanery $deaneryLivingstone;
    protected Deanery $deaneryKazungula;
    protected Parish $parishStMarys;
    protected Parish $parishHolyCross;
    protected Parish $parishKazungula;
    protected User $userMwansa;
    protected User $userMutale;
    protected User $userSuperAdmin;
    protected Category $category;
    protected RallyAccessService $accessService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessService = app(RallyAccessService::class);

        // Seed core deaneries & parishes
        $this->deaneryLivingstone = Deanery::create(['name' => 'Livingstone Deanery', 'code' => 'LIV']);
        $this->deaneryKazungula = Deanery::create(['name' => 'Kazungula Deanery', 'code' => 'KAZ']);

        $this->parishStMarys = Parish::create([
            'deanery_id' => $this->deaneryLivingstone->id,
            'name' => "St. Mary's Parish",
            'code' => 'STM',
        ]);

        $this->parishHolyCross = Parish::create([
            'deanery_id' => $this->deaneryLivingstone->id,
            'name' => 'Holy Cross Parish',
            'code' => 'HCP',
        ]);

        $this->parishKazungula = Parish::create([
            'deanery_id' => $this->deaneryKazungula->id,
            'name' => 'St. Theresa Kazungula',
            'code' => 'STK',
        ]);

        // Seed users
        $this->userMwansa = User::create([
            'name' => 'Mwansa Banda',
            'email' => 'mwansa@livingstoneyouth.org',
            'phone' => '+260971000001',
            'password' => Hash::make('secret123'),
            'role' => 'youth',
            'status' => 'approved',
            'parish_id' => $this->parishStMarys->id,
        ]);

        $this->userMutale = User::create([
            'name' => 'Mutale Phiri',
            'email' => 'mutale@livingstoneyouth.org',
            'phone' => '+260971000002',
            'password' => Hash::make('secret123'),
            'role' => 'youth',
            'status' => 'approved',
            'parish_id' => $this->parishKazungula->id,
        ]);

        $this->userSuperAdmin = User::create([
            'name' => 'Fr. Diocesan Admin',
            'email' => 'admin@dioceseoflivingstone.org',
            'phone' => '+260971000003',
            'password' => Hash::make('secret123'),
            'role' => 'super_admin',
            'status' => 'approved',
            'parish_id' => $this->parishStMarys->id,
        ]);

        $this->category = Category::create([
            'name' => 'Sacred Scripture',
            'slug' => 'sacred-scripture',
            'display_order' => 1,
        ]);

        // Create sample active questions
        for ($i = 1; $i <= 10; $i++) {
            Question::create([
                'category_id' => $this->category->id,
                'level' => 1,
                'question_text' => "Sample Catholic Scripture Question #{$i}?",
                'options' => ['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C', 'D' => 'Option D'],
                'correct_option_key' => 'A',
                'is_active' => true,
            ]);
        }
    }

    /**
     * TEST 1 — DIOCESE SCOPE
     * Youth from Diocese can enter Diocese Rally.
     */
    public function test_1_diocese_scope_allows_eligible_diocesan_youth(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Diocesan Youth Rally 2026',
            'scope_type' => 'diocese',
            'competition_type' => 'diocesan',
            'status' => 'live',
            'rally_pin' => 'LV-CATH-7K29X',
            'category_id' => $this->category->id,
        ]);

        $validation = $this->accessService->validateRallyAccess($rally, $this->userMwansa, 'LV-CATH-7K29X');

        $this->assertTrue($validation['allowed']);
        $this->assertNotNull($validation['participant']);
        $this->assertEquals('active', $validation['participant']->status);
    }

    /**
     * TEST 2 — DEANERY SCOPE
     * Youth from Deanery A can enter Deanery A Rally.
     * Youth from Deanery B cannot enter Deanery A Rally.
     */
    public function test_2_deanery_scope_restricts_to_selected_deanery(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Deanery Youth Rally',
            'scope_type' => 'deanery',
            'deanery_id' => $this->deaneryLivingstone->id,
            'status' => 'live',
            'rally_pin' => 'LV-DEAN-12345',
            'category_id' => $this->category->id,
        ]);

        // Mwansa belongs to St. Mary's (Livingstone Deanery) -> ALLOWED
        $resMwansa = $this->accessService->validateRallyAccess($rally, $this->userMwansa, 'LV-DEAN-12345');
        $this->assertTrue($resMwansa['allowed']);

        // Mutale belongs to Kazungula (Kazungula Deanery) -> REJECTED
        $resMutale = $this->accessService->validateRallyAccess($rally, $this->userMutale, 'LV-DEAN-12345');
        $this->assertFalse($resMutale['allowed']);
        $this->assertEquals('DEANERY_MISMATCH', $resMutale['error_code']);
    }

    /**
     * TEST 3 — PARISH SCOPE
     * Youth from Parish A can enter Parish A Rally.
     * Youth from Parish B cannot enter Parish A Rally.
     */
    public function test_3_parish_scope_restricts_to_selected_parish(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => "St. Mary's Parish Quiz Rally",
            'scope_type' => 'parish',
            'parish_id' => $this->parishStMarys->id,
            'status' => 'live',
            'rally_pin' => 'LV-PAR-99881',
            'category_id' => $this->category->id,
        ]);

        // Mwansa (St. Mary's) -> ALLOWED
        $resMwansa = $this->accessService->validateRallyAccess($rally, $this->userMwansa, 'LV-PAR-99881');
        $this->assertTrue($resMwansa['allowed']);

        // Mutale (Kazungula) -> REJECTED
        $resMutale = $this->accessService->validateRallyAccess($rally, $this->userMutale, 'LV-PAR-99881');
        $this->assertFalse($resMutale['allowed']);
        $this->assertEquals('PARISH_MISMATCH', $resMutale['error_code']);
    }

    /**
     * TEST 4 — CUSTOM RALLY
     * Mwansa receives code X -> Mwansa enters X -> SUCCESS.
     */
    public function test_4_custom_rally_allows_assigned_participant_with_correct_code(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Catholic Youth Challenge',
            'scope_type' => 'custom',
            'status' => 'live',
            'category_id' => $this->category->id,
        ]);

        $participant = $this->accessService->addCustomParticipant($rally, $this->userMwansa, $this->userSuperAdmin);
        $mwansaCode = $participant->access_code;

        $this->assertNotNull($mwansaCode);
        $this->assertStringStartsWith('LV26-', $mwansaCode);

        // Mwansa uses his code
        $result = $this->accessService->validateRallyAccess($rally, $this->userMwansa, $mwansaCode);
        $this->assertTrue($result['allowed']);
        $this->assertEquals($participant->id, $result['participant']->id);
    }

    /**
     * TEST 5 — CUSTOM CODE SHARING SECURITY
     * Mwansa gives his code to Mutale.
     * Mutale attempts to use Mwansa's code while authenticated as Mutale -> MUST FAIL.
     */
    public function test_5_custom_code_cannot_be_used_by_another_authenticated_user(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Catholic Youth Challenge',
            'scope_type' => 'custom',
            'status' => 'live',
            'category_id' => $this->category->id,
        ]);

        // Only Mwansa is invited
        $mwansaParticipant = $this->accessService->addCustomParticipant($rally, $this->userMwansa, $this->userSuperAdmin);
        $mwansaCode = $mwansaParticipant->access_code;

        // Mutale logs in and tries to use Mwansa's code
        $result = $this->accessService->validateRallyAccess($rally, $this->userMutale, $mwansaCode);

        $this->assertFalse($result['allowed']);
        $this->assertNull($result['participant']);
    }

    /**
     * TEST 6 — WRONG RALLY CODE
     * User has code for Rally A. Attempts to use it on Rally B -> MUST FAIL.
     */
    public function test_6_access_code_from_wrong_rally_is_rejected(): void
    {
        $rallyA = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Rally Alpha',
            'scope_type' => 'custom',
            'status' => 'live',
        ]);

        $rallyB = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Rally Beta',
            'scope_type' => 'custom',
            'status' => 'live',
        ]);

        $partA = $this->accessService->addCustomParticipant($rallyA, $this->userMwansa, $this->userSuperAdmin);

        // Attempt to use Rally A code on Rally B
        $result = $this->accessService->validateRallyAccess($rallyB, $this->userMwansa, $partA->access_code);
        $this->assertFalse($result['allowed']);
    }

    /**
     * TEST 7 — REMOVED PARTICIPANT
     * Participant removed -> Old code used -> MUST FAIL.
     */
    public function test_7_removed_participant_code_is_immediately_revoked(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Invitational',
            'scope_type' => 'custom',
            'status' => 'live',
        ]);

        $participant = $this->accessService->addCustomParticipant($rally, $this->userMwansa, $this->userSuperAdmin);
        $code = $participant->access_code;

        // Admin removes Mwansa
        $this->accessService->removeParticipant($rally, $this->userMwansa, $this->userSuperAdmin);

        // Mwansa tries to enter with old code
        $result = $this->accessService->validateRallyAccess($rally, $this->userMwansa, $code);

        $this->assertFalse($result['allowed']);
        $this->assertEquals('REMOVED', $result['error_code']);
    }

    /**
     * TEST 8 — REGENERATED CODE
     * Old code used after regeneration -> MUST FAIL.
     * New code used by correct user -> SUCCESS.
     */
    public function test_8_regenerated_code_invalidates_old_code_and_activates_new(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Invitational',
            'scope_type' => 'custom',
            'status' => 'live',
        ]);

        $participant = $this->accessService->addCustomParticipant($rally, $this->userMwansa, $this->userSuperAdmin);
        $oldCode = $participant->access_code;

        // Admin regenerates code
        $newCode = $this->accessService->regenerateParticipantCode($participant, $this->userSuperAdmin);

        $this->assertNotEquals($oldCode, $newCode);

        // Old code fails
        $resOld = $this->accessService->validateRallyAccess($rally, $this->userMwansa, $oldCode);
        $this->assertFalse($resOld['allowed']);

        // New code passes
        $resNew = $this->accessService->validateRallyAccess($rally, $this->userMwansa, $newCode);
        $this->assertTrue($resNew['allowed']);
    }

    /**
     * TEST 9 — REJECTED JOIN REQUEST
     * User request rejected -> No access -> No access code generated.
     */
    public function test_9_rejected_join_request_denies_access_and_generates_no_code(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Youth Bible Challenge',
            'scope_type' => 'custom',
            'status' => 'live',
        ]);

        $request = $this->accessService->submitJoinRequest($rally, $this->userMwansa, 'Please let me join');
        $this->accessService->rejectJoinRequest($request, $this->userSuperAdmin, 'Capacity full for this round');

        $this->assertEquals('rejected', $request->fresh()->status);
        $this->assertEquals('Capacity full for this round', $request->fresh()->rejection_reason);

        // Mwansa tries to enter
        $result = $this->accessService->validateRallyAccess($rally, $this->userMwansa);
        $this->assertFalse($result['allowed']);
        $this->assertNull(RallyParticipant::where('rally_id', $rally->id)->where('user_id', $this->userMwansa->id)->first());
    }

    /**
     * TEST 10 — APPROVED JOIN REQUEST
     * Request approved -> Participant created -> Unique code generated -> User authorized.
     */
    public function test_10_approved_join_request_creates_participant_and_generates_unique_code(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Youth Bible Challenge',
            'scope_type' => 'custom',
            'status' => 'live',
        ]);

        $request = $this->accessService->submitJoinRequest($rally, $this->userMwansa, 'I am ready');
        $participant = $this->accessService->approveJoinRequest($request, $this->userSuperAdmin);

        $this->assertEquals('approved', $request->fresh()->status);
        $this->assertNotNull($participant->access_code);
        $this->assertEquals('approved', $participant->status);

        // Mwansa can enter with generated code
        $result = $this->accessService->validateRallyAccess($rally, $this->userMwansa, $participant->access_code);
        $this->assertTrue($result['allowed']);
    }

    /**
     * TEST 11 — DUPLICATE JOIN REQUEST
     * User submits request twice -> Only one pending request.
     */
    public function test_11_duplicate_pending_join_requests_are_prevented(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Youth Bible Challenge',
            'scope_type' => 'custom',
            'status' => 'live',
        ]);

        $req1 = $this->accessService->submitJoinRequest($rally, $this->userMwansa, 'First try');
        $req2 = $this->accessService->submitJoinRequest($rally, $this->userMwansa, 'Second try');

        $this->assertEquals($req1->id, $req2->id);
        $this->assertEquals(1, RallyJoinRequest::where('rally_id', $rally->id)->where('user_id', $this->userMwansa->id)->count());
    }

    /**
     * TEST 12 — CLOSED OR OUT OF WINDOW RALLY
     * Rally closed / draft / cancelled -> New participation denied.
     */
    public function test_12_closed_or_out_of_window_rally_denies_entry(): void
    {
        $closedRally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Past Rally 2025',
            'scope_type' => 'diocese',
            'status' => 'closed',
        ]);

        $resultClosed = $this->accessService->validateRallyAccess($closedRally, $this->userMwansa);
        $this->assertFalse($resultClosed['allowed']);
        $this->assertEquals('RALLY_CLOSED', $resultClosed['error_code']);

        $futureRally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Future Rally 2027',
            'scope_type' => 'diocese',
            'status' => 'scheduled',
            'start_time' => now()->addDays(5),
            'end_time' => now()->addDays(6),
        ]);

        $resultFuture = $this->accessService->validateRallyAccess($futureRally, $this->userMwansa);
        $this->assertFalse($resultFuture['allowed']);
        $this->assertEquals('NOT_STARTED', $resultFuture['error_code']);
    }

    /**
     * TEST 13 — WRONG ROLE ENFORCEMENT
     * User without appropriate role cannot enter.
     */
    public function test_13_non_youth_role_participation_enforcement(): void
    {
        $nonYouthUser = User::create([
            'name' => 'Deanery Officer',
            'email' => 'officer@example.com',
            'phone' => '+260971000099',
            'password' => Hash::make('password'),
            'role' => 'deanery_admin',
            'status' => 'approved',
        ]);

        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Diocesan Youth Bible Rally',
            'scope_type' => 'diocese',
            'status' => 'live',
        ]);

        $result = $this->accessService->validateRallyAccess($rally, $nonYouthUser);
        $this->assertFalse($result['allowed']);
        $this->assertEquals('WRONG_ROLE', $result['error_code']);
    }

    /**
     * TEST 14 — DIRECT URL ACCESS
     * Unauthorized user manually accessing /quiz/play?competition=... is blocked.
     */
    public function test_14_direct_url_access_cannot_bypass_authorization(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Deanery Rally',
            'scope_type' => 'deanery',
            'deanery_id' => $this->deaneryLivingstone->id,
            'status' => 'live',
            'category_id' => $this->category->id,
        ]);

        // Mutale belongs to Kazungula (not Livingstone) -> accessing URL must redirect with flash error
        $response = $this->actingAs($this->userMutale)
            ->get(route('quiz.runner', ['competition' => $rally->id]));

        $response->assertRedirect(route('arena.hub'));
        $response->assertSessionHas('error');
    }

    /**
     * TEST 15 — CONCURRENCY & UNIQUE DATABASE CONSTRAINTS
     * Unique constraints prevent duplicate participant records.
     */
    public function test_15_concurrency_and_database_unique_constraints_prevent_duplicate_participants(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Livingstone Invitational',
            'scope_type' => 'custom',
            'status' => 'live',
        ]);

        $part1 = $this->accessService->addCustomParticipant($rally, $this->userMwansa, $this->userSuperAdmin);
        $part2 = $this->accessService->addCustomParticipant($rally, $this->userMwansa, $this->userSuperAdmin);

        $this->assertEquals($part1->id, $part2->id);
        $this->assertEquals(1, RallyParticipant::where('rally_id', $rally->id)->where('user_id', $this->userMwansa->id)->count());
    }

    /**
     * TEST 16 — ENROLLED PARTICIPANT EXCLUDED FROM DISCOVERY LIST
     * When user is already registered in a rally, it is excluded from discoverable rallies.
     */
    public function test_16_enrolled_participant_excluded_from_discoverable_rallies(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'St. Theresa Youth Invitational',
            'scope_type' => 'custom',
            'status' => 'scheduled',
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(7),
            'is_public' => true,
            'join_requests_enabled' => true,
        ]);

        // Prior to enrollment, it appears in discoverable rallies
        $availableBefore = $this->accessService->getAvailableRalliesForUser($this->userMwansa);
        $this->assertTrue($availableBefore->contains('id', $rally->id));

        // Enroll Mwansa
        $this->accessService->addCustomParticipant($rally, $this->userMwansa, $this->userSuperAdmin);

        // After enrollment, it must NOT appear in discoverable rallies (shows in My Rallies with code instead)
        $availableAfter = $this->accessService->getAvailableRalliesForUser($this->userMwansa);
        $this->assertFalse($availableAfter->contains('id', $rally->id));

        $myRallies = $this->accessService->getUserRallies($this->userMwansa);
        $this->assertCount(1, $myRallies['upcoming']);
        $this->assertEquals($rally->id, $myRallies['upcoming'][0]['rally']->id);
    }

    /**
     * TEST 17 — 1-ATTEMPT ENFORCEMENT & SCORE/ANSWER REVIEW
     * Once a youth submits a rally quiz, repeat entry is blocked, and results/answers can be reviewed.
     */
    public function test_17_single_attempt_enforced_and_score_review_enabled(): void
    {
        $rally = DiocesanCompetition::create([
            'created_by' => $this->userSuperAdmin->id,
            'title' => 'Choma Deanery Championship',
            'scope_type' => 'deanery',
            'deanery_id' => $this->deaneryLivingstone->id,
            'status' => 'active',
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(2),
            'time_limit_seconds' => 15,
            'question_count' => 10,
        ]);

        // 1. Initially user is allowed entry
        $entry1 = $this->accessService->validateRallyAccess($rally, $this->userMwansa);
        $this->assertTrue($entry1['allowed']);

        // 2. Simulate User completing the quiz attempt
        $participant = RallyParticipant::updateOrCreate(
            ['rally_id' => $rally->id, 'user_id' => $this->userMwansa->id],
            [
                'status' => 'completed',
                'score' => 850,
                'completed_at' => now(),
                'metadata' => [
                    'correct_count' => 8,
                    'total_questions' => 10,
                ],
            ]
        );

        // 3. Subsequent entry attempt must be denied with ALREADY_COMPLETED
        $entry2 = $this->accessService->validateRallyAccess($rally, $this->userMwansa);
        $this->assertFalse($entry2['allowed']);
        $this->assertEquals('ALREADY_COMPLETED', $entry2['error_code']);

        // 4. Rally is grouped in user's completed list
        $userRallies = $this->accessService->getUserRallies($this->userMwansa);
        $this->assertCount(1, $userRallies['completed']);
        $this->assertEquals(850, $userRallies['completed'][0]['participant']->score);

        // 5. ArenaHub Livewire component can open review modal
        \Livewire\Livewire::actingAs($this->userMwansa)
            ->test(\App\Livewire\ArenaHub::class)
            ->call('openRallyReview', $participant->id)
            ->assertSet('showRallyReviewModal', true)
            ->assertSee('Choma Deanery Championship')
            ->assertSee('850 pts');
    }
}
