<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\Profile;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BiometricAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Parish $parish;

    protected function setUp(): void
    {
        parent::setUp();

        $deanery = Deanery::create(['code' => 'LIV', 'name' => 'Livingstone Deanery']);
        $this->parish = Parish::create([
            'deanery_id' => $deanery->id,
            'name' => "St. Theresa's Cathedral",
            'location' => 'Livingstone',
        ]);

        $this->user = User::create([
            'parish_id' => $this->parish->id,
            'name' => 'Mutale Mwamba',
            'email' => 'mutale@example.com',
            'phone' => '+260970000001',
            'password' => bcrypt('password123'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 100,
            'level' => 2,
        ]);
    }

    public function test_user_model_generates_and_verifies_biometric_token(): void
    {
        $this->assertFalse($this->user->hasBiometricEnabled());

        $rawToken = $this->user->generateBiometricToken('mock-credential-id-123');
        $this->user->refresh();

        $this->assertTrue($this->user->hasBiometricEnabled());
        $this->assertNotEmpty($this->user->biometric_token_hash);
        $this->assertEquals('mock-credential-id-123', $this->user->biometric_credential_id);
        $this->assertNotNull($this->user->biometric_enabled_at);

        // Valid token verification
        $this->assertTrue($this->user->verifyBiometricToken($rawToken));

        // Invalid token verification
        $this->assertFalse($this->user->verifyBiometricToken('invalid-fake-token'));

        // Disable biometrics
        $this->user->disableBiometrics();
        $this->user->refresh();

        $this->assertFalse($this->user->hasBiometricEnabled());
        $this->assertNull($this->user->biometric_credential_id);
        $this->assertFalse($this->user->verifyBiometricToken($rawToken));
    }

    public function test_user_can_login_via_biometrics_in_login_component(): void
    {
        $rawToken = $this->user->generateBiometricToken();
        $this->user->refresh();

        $this->assertGuest();

        Livewire::test(Login::class)
            ->call('biometricLogin', $this->user->id, $rawToken)
            ->assertDispatched('biometric-auth-success')
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($this->user);
    }

    public function test_user_can_login_via_hardware_credential_id(): void
    {
        $this->user->generateBiometricToken('hardware-passkey-xyz');
        $this->user->refresh();

        $this->assertGuest();

        Livewire::test(Login::class)
            ->call('biometricLoginByCredential', 'hardware-passkey-xyz')
            ->assertDispatched('biometric-auth-success')
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($this->user);
    }

    public function test_biometric_login_fails_with_invalid_token(): void
    {
        $this->user->generateBiometricToken();
        $this->user->refresh();

        $this->assertGuest();

        Livewire::test(Login::class)
            ->call('biometricLogin', $this->user->id, 'wrong-token')
            ->assertDispatched('biometric-auth-failed')
            ->assertHasErrors(['identifier']);

        $this->assertGuest();
    }

    public function test_biometric_login_fails_for_deactivated_user(): void
    {
        $rawToken = $this->user->generateBiometricToken();
        $this->user->update(['deactivated_at' => now()]);
        $this->user->refresh();

        Livewire::test(Login::class)
            ->call('biometricLogin', $this->user->id, $rawToken)
            ->assertHasErrors(['identifier']);

        $this->assertGuest();
    }

    public function test_youth_can_enable_and_disable_biometrics_from_profile(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Profile::class)
            ->assertSet('biometricEnabled', false)
            ->call('enableBiometrics', 'cred-id-abc')
            ->assertSet('biometricEnabled', true)
            ->assertDispatched('biometric-enrolled-on-device')
            ->call('disableBiometrics')
            ->assertSet('biometricEnabled', false)
            ->assertDispatched('biometric-revoked-on-device');

        $this->user->refresh();
        $this->assertFalse($this->user->hasBiometricEnabled());
    }

    public function test_login_page_renders_biometric_button_on_main_form(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign In with Fingerprint / Face ID');
        $response->assertSee('Sign In with Password');
    }
}
