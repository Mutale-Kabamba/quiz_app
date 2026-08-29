<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileAppFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guests_are_redirected_to_login_screen_first()
    {
        // Unauthenticated guests cannot see any app content before logging in
        $this->get('/')->assertRedirect('/login');
        $this->get('/quiz')->assertRedirect('/login');
        $this->get('/leaderboard')->assertRedirect('/login');
        $this->get('/study')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_login_and_register_screens_render_for_guests()
    {
        $this->get('/login')->assertStatus(200)->assertSee('Catholic Youth Ministry');
        $this->get('/register')->assertStatus(200)->assertSee('Parish Youth Registration');
    }

    public function test_authenticated_youth_can_access_dashboard_and_features()
    {
        $youth = User::where('role', 'youth')->first();

        $this->actingAs($youth)->get('/')->assertStatus(200)->assertSee('Competition Arena');
        $this->actingAs($youth)->get('/quiz')->assertStatus(200);
        $this->actingAs($youth)->get('/leaderboard')->assertStatus(200);
        $this->actingAs($youth)->get('/study')->assertStatus(200);
        $this->actingAs($youth)->get('/profile')->assertStatus(200);
    }

    public function test_chairperson_approval_access_restricted_to_chairperson()
    {
        $youth = User::where('role', 'youth')->first();
        $chairperson = User::where('role', 'chairperson')->first();

        // Standard youth is forbidden
        $this->actingAs($youth)->get('/approvals')->assertStatus(403);

        // Chairperson is authorized
        $this->actingAs($chairperson)->get('/approvals')->assertStatus(200);
    }
}
