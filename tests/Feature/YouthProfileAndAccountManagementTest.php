<?php

namespace Tests\Feature;

use App\Livewire\Profile;
use App\Models\AuditLog;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\ParishTransfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class YouthProfileAndAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $youth;
    protected Parish $parishA;
    protected Parish $parishB;

    protected function setUp(): void
    {
        parent::setUp();

        $deanery = Deanery::create(['code' => 'LIV', 'name' => 'Livingstone Deanery']);
        $this->parishA = Parish::create(['deanery_id' => $deanery->id, 'code' => 'STT', 'name' => "St. Theresa's Cathedral"]);
        $this->parishB = Parish::create(['deanery_id' => $deanery->id, 'code' => 'SJM', 'name' => 'St. Joseph Maramba']);

        $this->youth = User::create([
            'parish_id' => $this->parishA->id,
            'name' => 'Mwamba Mutale',
            'phone' => '+260971112233',
            'email' => 'mwamba@example.com',
            'password' => Hash::make('password123'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 450,
            'level' => 2,
            'current_streak' => 5,
        ]);
    }

    public function test_youth_can_view_profile_and_account_sections(): void
    {
        $this->actingAs($this->youth);

        $response = $this->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Mwamba Mutale');
        $response->assertSee("St. Theresa's Cathedral");
        $response->assertSee('450 XP');
        $response->assertSee('Personal Information');
        $response->assertSee('Account Security');
    }

    public function test_youth_can_update_personal_profile_details(): void
    {
        $this->actingAs($this->youth);

        Livewire::test(Profile::class)
            ->set('name', 'Mwamba K. Mutale')
            ->set('phone', '+260979998877')
            ->set('email', 'mwamba.new@example.com')
            ->set('dob', '2004-05-15')
            ->set('gender', 'male')
            ->call('saveProfile')
            ->assertHasNoErrors();

        $this->youth->refresh();
        $this->assertEquals('Mwamba K. Mutale', $this->youth->name);
        $this->assertEquals('+260979998877', $this->youth->phone);
        $this->assertEquals('mwamba.new@example.com', $this->youth->email);
        $this->assertEquals('2004-05-15', $this->youth->dob->format('Y-m-d'));
        $this->assertEquals('male', $this->youth->gender);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->youth->id,
            'action' => 'profile_info_updated',
        ]);
    }

    public function test_youth_can_upload_and_remove_avatar(): void
    {
        Storage::fake('public');
        $this->actingAs($this->youth);

        $file = UploadedFile::fake()->image('avatar.jpg', 300, 300);

        Livewire::test(Profile::class)
            ->set('avatarFile', $file)
            ->call('uploadAvatar')
            ->assertHasNoErrors();

        $this->youth->refresh();
        $this->assertNotNull($this->youth->avatar_path);
        Storage::disk('public')->assertExists($this->youth->avatar_path);

        // Test remove avatar
        Livewire::test(Profile::class)
            ->call('removeAvatar')
            ->assertHasNoErrors();

        $this->youth->refresh();
        $this->assertNull($this->youth->avatar_path);
    }

    public function test_youth_can_change_password_with_valid_current_password(): void
    {
        $this->actingAs($this->youth);

        // Attempt with wrong current password
        Livewire::test(Profile::class)
            ->set('currentPassword', 'wrong-pass')
            ->set('newPassword', 'newsecret123')
            ->set('newPasswordConfirmation', 'newsecret123')
            ->call('changePassword')
            ->assertHasErrors(['currentPassword']);

        // Attempt with valid current password
        Livewire::test(Profile::class)
            ->set('currentPassword', 'password123')
            ->set('newPassword', 'newsecret123')
            ->set('newPasswordConfirmation', 'newsecret123')
            ->call('changePassword')
            ->assertHasNoErrors();

        $this->youth->refresh();
        $this->assertTrue(Hash::check('newsecret123', $this->youth->password));
        $this->assertNotNull($this->youth->last_password_changed_at);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->youth->id,
            'action' => 'profile_password_changed',
        ]);
    }

    public function test_youth_can_request_parish_transfer(): void
    {
        $this->actingAs($this->youth);

        Livewire::test(Profile::class)
            ->set('targetParishId', $this->parishB->id)
            ->set('transferReason', 'Family moved to Maramba neighborhood')
            ->call('requestParishTransfer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('parish_transfers', [
            'user_id' => $this->youth->id,
            'from_parish_id' => $this->parishA->id,
            'to_parish_id' => $this->parishB->id,
            'reason' => 'Family moved to Maramba neighborhood',
            'status' => 'pending',
        ]);
    }

    public function test_youth_can_update_preferences_and_privacy(): void
    {
        $this->actingAs($this->youth);

        Livewire::test(Profile::class)
            ->set('notifyFormation', false)
            ->set('showNameInRankings', false)
            ->call('savePreferences')
            ->assertHasNoErrors();

        $this->youth->refresh();
        $this->assertFalse($this->youth->preferences['notifications']['formation']);
        $this->assertFalse($this->youth->preferences['privacy']['show_name']);
    }

    public function test_youth_can_deactivate_account_safely(): void
    {
        $this->actingAs($this->youth);

        Livewire::test(Profile::class)
            ->call('deactivateAccount')
            ->assertRedirect('/login');

        $this->youth->refresh();
        $this->assertTrue($this->youth->isDeactivated());
        $this->assertEquals('pending', $this->youth->status);
        $this->assertGuest();
    }
}
