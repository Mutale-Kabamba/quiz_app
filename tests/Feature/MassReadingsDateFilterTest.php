<?php

namespace Tests\Feature;

use App\Livewire\MobileDashboard;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MassReadingsDateFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $deanery = Deanery::create(['code' => 'LIV', 'name' => 'Livingstone Deanery']);
        $parish = Parish::create(['deanery_id' => $deanery->id, 'name' => 'St. Theresa Cathedral Parish', 'location' => 'Livingstone']);

        $this->user = User::create([
            'parish_id' => $parish->id,
            'name' => 'Test Youth',
            'email' => 'youth@example.com',
            'phone' => '+260970000099',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 150,
            'level' => 1,
        ]);
    }

    public function test_mass_readings_shows_date_filter_and_supports_previous_and_next_day(): void
    {
        $today = Carbon::now();
        $yesterday = $today->copy()->subDay();
        $tomorrow = $today->copy()->addDay();

        $component = Livewire::actingAs($this->user)
            ->test(MobileDashboard::class)
            ->assertSee("Today's Mass Readings")
            ->assertSee($today->format('M j'))
            ->call('previousDayReadings')
            ->assertSet('readingsDate', $yesterday->format('Y-m-d'))
            ->assertSee($yesterday->format('M j'))
            ->assertSee('Reset Today')
            ->call('nextDayReadings')
            ->call('nextDayReadings')
            ->assertSet('readingsDate', $tomorrow->format('Y-m-d'))
            ->assertSee($tomorrow->format('M j'))
            ->call('todayReadings')
            ->assertSet('readingsDate', null)
            ->assertSee("Today's Mass Readings");
    }

    public function test_custom_date_filtering_updates_liturgical_readings(): void
    {
        // Solemnity of Mary (Jan 1)
        Livewire::actingAs($this->user)
            ->test(MobileDashboard::class)
            ->call('setReadingsDate', '2026-01-01')
            ->assertSet('readingsDate', '2026-01-01')
            ->assertSee('Solemnity of Mary, Mother of God')
            ->assertSee('Numbers 6:22–27')
            ->assertSee('Luke 2:16–21')
            // St. Charles Lwanga (June 3)
            ->call('setReadingsDate', '2026-06-03')
            ->assertSet('readingsDate', '2026-06-03')
            ->assertSee('Memorial of Saint Charles Lwanga and Companions, Martyrs (Uganda)')
            ->assertSee('2 Maccabees 7:1–2, 9–14')
            ->assertSee('Matthew 10:17–22');
    }

    public function test_full_readings_modal_displays_filtered_date_readings_not_today(): void
    {
        // Set date to Friday Sep 4, 2026 and open the full readings modal
        Livewire::actingAs($this->user)
            ->test(MobileDashboard::class)
            ->call('setReadingsDate', '2026-09-04')
            ->assertSet('readingsDate', '2026-09-04')
            ->call('openReadingsModal')
            ->assertSet('showReadingsModal', true)
            ->assertSee('Friday, September 4, 2026')
            ->assertSee('Luke 5:33-39')
            ->assertSee('No one pours new wine into old wineskins')
            ->call('setReadingsTab', 'reading1')
            ->assertSee('1 Corinthians 4:1-5')
            ->call('setReadingsTab', 'psalm')
            ->assertSee('Psalm 37:3-4')
            ->call('closeReadingsModal')
            ->assertSet('showReadingsModal', false);
    }
}
