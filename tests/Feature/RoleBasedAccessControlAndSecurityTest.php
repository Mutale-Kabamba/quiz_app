<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\ChairpersonApproval;
use App\Livewire\ParishAdminDashboard;
use App\Livewire\Profile;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class RoleBasedAccessControlAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $chairpersonA;
    protected User $chairpersonB;
    protected User $youthA;
    protected User $youthB;
    protected Parish $parishA;
    protected Parish $parishB;

    protected function setUp(): void
    {
        parent::setUp();

        $deanery = Deanery::create(['code' => 'LIV', 'name' => 'Livingstone Deanery']);
        $this->parishA = Parish::create(['deanery_id' => $deanery->id, 'code' => 'STT', 'name' => "St. Theresa's Cathedral"]);
        $this->parishB = Parish::create(['deanery_id' => $deanery->id, 'code' => 'SJM', 'name' => 'St. Joseph Maramba']);

        $this->superAdmin = User::create([
            'name' => 'Diocesan Admin',
            'phone' => '+260970000001',
            'email' => 'admin@livingstonediocese.org',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'status' => 'approved',
        ]);

        $this->chairpersonA = User::create([
            'parish_id' => $this->parishA->id,
            'name' => 'Chairperson St Theresa',
            'phone' => '+260970000002',
            'email' => 'chair_a@livingstonediocese.org',
            'password' => Hash::make('password123'),
            'role' => 'chairperson',
            'status' => 'approved',
        ]);

        $this->chairpersonB = User::create([
            'parish_id' => $this->parishB->id,
            'name' => 'Chairperson St Joseph',
            'phone' => '+260970000003',
            'email' => 'chair_b@livingstonediocese.org',
            'password' => Hash::make('password123'),
            'role' => 'chairperson',
            'status' => 'approved',
        ]);

        $this->youthA = User::create([
            'parish_id' => $this->parishA->id,
            'name' => 'Youth Member A',
            'phone' => '+260970000004',
            'email' => 'youth_a@example.com',
            'password' => Hash::make('password123'),
            'role' => 'youth',
            'status' => 'pending',
        ]);

        $this->youthB = User::create([
            'parish_id' => $this->parishB->id,
            'name' => 'Youth Member B',
            'phone' => '+260970000005',
            'email' => 'youth_b@example.com',
            'password' => Hash::make('password123'),
            'role' => 'youth',
            'status' => 'pending',
        ]);
    }

    public function test_post_login_role_based_routing(): void
    {
        // 1. Super Admin login routes to /diocese
        Livewire::test(Login::class)
            ->set('identifier', '+260970000001')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect('/diocese');

        $this->assertAuthenticatedAs($this->superAdmin);

        // 2. Chairperson login routes to /parish
        Livewire::test(Login::class)
            ->set('identifier', '+260970000002')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect('/parish');

        $this->assertAuthenticatedAs($this->chairpersonA);

        // 3. Youth login routes to /
        Livewire::test(Login::class)
            ->set('identifier', '+260970000004')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($this->youthA);
    }

    public function test_youth_cannot_access_parish_admin_or_approvals_or_diocese_routes(): void
    {
        $this->actingAs($this->youthA);

        // Accessing /parish should return 403 Forbidden
        $responseParish = $this->get('/parish');
        $responseParish->assertStatus(403);

        // Accessing /approvals should return 403 Forbidden
        $responseApprovals = $this->get('/approvals');
        $responseApprovals->assertStatus(403);

        // Accessing /diocese should return 403 Forbidden
        $responseDiocese = $this->get('/diocese');
        $responseDiocese->assertStatus(403);
    }

    public function test_super_admin_can_access_diocese_dashboard_and_create_parish(): void
    {
        $this->actingAs($this->superAdmin);

        $responseDiocese = $this->get('/diocese');
        $responseDiocese->assertStatus(200);
        $responseDiocese->assertSee('Diocesan Command Center');
        $responseDiocese->assertSee('Livingstone Diocese');

        // Test creating parish directly in Diocese dashboard
        Livewire::test(\App\Livewire\DioceseDashboard::class)
            ->set('newParishName', 'St. Lawrence Parish')
            ->set('newParishCode', 'STL')
            ->set('newParishDeaneryId', $this->parishA->deanery_id)
            ->call('createParish')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('parishes', [
            'name' => 'St. Lawrence Parish',
            'code' => 'STL',
        ]);
    }

    public function test_parish_admin_can_access_parish_dashboard_and_approvals(): void
    {
        $this->actingAs($this->chairpersonA);

        $responseParish = $this->get('/parish');
        $responseParish->assertStatus(200);
        $responseParish->assertSee("St. Theresa's Cathedral");
        $responseParish->assertSee('Parish Administration');

        $responseApprovals = $this->get('/approvals');
        $responseApprovals->assertStatus(200);
        $responseApprovals->assertSee('Youth Member A');
    }

    public function test_parish_admin_cannot_approve_youth_from_another_parish_idor(): void
    {
        $this->actingAs($this->chairpersonA);

        // Attempt to approve youthB who belongs to parishB
        Livewire::test(ChairpersonApproval::class)
            ->call('approve', $this->youthB->id)
            ->assertStatus(403);

        $this->youthB->refresh();
        $this->assertEquals('pending', $this->youthB->status);
    }

    public function test_parish_admin_can_approve_youth_from_own_parish(): void
    {
        $this->actingAs($this->chairpersonA);

        Livewire::test(ChairpersonApproval::class)
            ->call('approve', $this->youthA->id)
            ->assertStatus(200);

        $this->youthA->refresh();
        $this->assertEquals('approved', $this->youthA->status);
        $this->assertEquals($this->chairpersonA->id, $this->youthA->approved_by);
    }

    public function test_youth_cannot_escalate_role_via_profile_update(): void
    {
        $this->actingAs($this->youthA);

        Livewire::test(Profile::class)
            ->set('name', 'Privilege Escalation Attempt')
            ->set('phone', '+260971234567')
            ->call('saveProfile')
            ->assertHasNoErrors();

        $this->youthA->refresh();
        $this->assertEquals('youth', $this->youthA->role);
        $this->assertEquals('Privilege Escalation Attempt', $this->youthA->name);
    }

    public function test_parish_policy_restricts_parish_creation_to_super_admin(): void
    {
        $this->assertTrue(Gate::forUser($this->superAdmin)->allows('create', Parish::class));
        $this->assertFalse(Gate::forUser($this->chairpersonA)->allows('create', Parish::class));
        $this->assertFalse(Gate::forUser($this->youthA)->allows('create', Parish::class));
    }
}
