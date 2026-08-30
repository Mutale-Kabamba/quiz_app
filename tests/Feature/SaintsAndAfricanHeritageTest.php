<?php

namespace Tests\Feature;

use App\Models\Deanery;
use App\Models\Parish;
use App\Models\SaintProfile;
use App\Models\User;
use Database\Seeders\SaintsAndAfricanHeritageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaintsAndAfricanHeritageTest extends TestCase
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
            'name' => 'Francis Mwape',
            'email' => 'francis@example.com',
            'phone' => '+260970000003',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
            'xp' => 200,
            'level' => 2,
        ]);

        $this->seed(SaintsAndAfricanHeritageSeeder::class);
    }

    public function test_saints_directory_renders_and_lists_all_patrons(): void
    {
        $response = $this->actingAs($this->user)->get('/saints');

        $response->assertStatus(200);
        $response->assertSee('Saints &amp; African Heritage', false);
        $response->assertSee('St. Theresa of the Child Jesus');
        $response->assertSee('St. Charles Lwanga and Companions');
        $response->assertSee('St. Josephine Bakhita');
        $response->assertSee('St. Augustine of Hippo');
    }

    public function test_every_category_has_at_least_10_saints_and_shows_numbers(): void
    {
        $test = \Livewire\Livewire::actingAs($this->user)
            ->test(\App\Livewire\SaintsHeritageHub::class);

        $counts = $test->get('counts');

        $this->assertGreaterThanOrEqual(10, $counts['african'], 'African Heritage should have at least 10 saints');
        $this->assertGreaterThanOrEqual(10, $counts['youth'], 'Youth Patrons should have at least 10 saints');
        $this->assertGreaterThanOrEqual(10, $counts['martyrs'], 'Martyrs of Faith should have at least 10 saints');
        $this->assertGreaterThanOrEqual(10, $counts['doctors'], 'Doctors of Church should have at least 10 saints');
        $this->assertGreaterThanOrEqual(10, $counts['fathers'], 'African Popes & Fathers should have at least 10 saints');

        $test->assertSee("All Saints ({$counts['all']})")
            ->assertSee("African Heritage ({$counts['african']})")
            ->assertSee("Youth Patrons ({$counts['youth']})")
            ->assertSee("Martyrs of Faith ({$counts['martyrs']})")
            ->assertSee("Doctors of Church ({$counts['doctors']})")
            ->assertSee("African Popes &amp; Fathers ({$counts['fathers']})", false);
    }

    public function test_saints_directory_supports_search_and_filtering(): void
    {
        \Livewire\Livewire::actingAs($this->user)
            ->test(\App\Livewire\SaintsHeritageHub::class)
            ->assertSee('St. Theresa of the Child Jesus')
            ->assertSee('St. Charles Lwanga')
            ->set('search', 'Augustine')
            ->assertSee('St. Augustine of Hippo')
            ->assertSee('Search Results')
            ->assertDontSee('St. Josephine Bakhita')
            ->set('search', '')
            ->set('activeFilter', 'african')
            ->assertSee('St. Josephine Bakhita')
            ->assertSee('St. Charles Lwanga')
            ->assertSee('African Heritage')
            ->assertDontSee('St. Theresa of the Child Jesus')
            ->set('activeFilter', 'martyrs')
            ->assertSee('16 Patrons')
            ->assertSee('Martyrs of Faith')
            ->set('activeFilter', 'youth')
            ->assertSee('Youth Patrons')
            ->assertSee('St. Aloysius Gonzaga')
            ->assertSee('St. Dominic Savio')
            ->assertSee('Blessed Carlo Acutis')
            ->set('activeFilter', 'doctors')
            ->assertSee('Doctors of the Church')
            ->assertSee('St. Thomas Aquinas')
            ->assertSee('St. Catherine of Siena')
            ->assertSee('St. Teresa of Ávila')
            ->set('activeFilter', 'fathers')
            ->assertSee('African Popes &amp; Church Fathers', false)
            ->assertSee('Pope St. Victor I')
            ->assertSee('Pope St. Miltiades')
            ->assertSee('Pope St. Gelasius I');
    }

    public function test_saint_detail_full_page_renders_complete_profile(): void
    {
        $saint = SaintProfile::where('slug', 'st-charles-lwanga')->firstOrFail();

        $response = $this->actingAs($this->user)->get('/saints/' . $saint->slug);

        $response->assertStatus(200);
        $response->assertSee('St. Charles Lwanga and Companions');
        $response->assertSee('Martyrs of Uganda');
        $response->assertSee('Life &amp; Sacred Witness', false);
        $response->assertSee('Intercessory Prayer');
        $response->assertSee('Namugongo');
        $response->assertSee('Moral Purity');
    }

    public function test_cathedral_patroness_saint_detail_renders(): void
    {
        $saint = SaintProfile::where('slug', 'st-theresa-of-lisieux')->firstOrFail();

        $response = $this->actingAs($this->user)->get('/saints/' . $saint->slug);

        $response->assertStatus(200);
        $response->assertSee('St. Theresa of the Child Jesus');
        $response->assertSee('Diocesan Cathedral Patroness');
        $response->assertSee('Little Way');
    }

    public function test_mobile_dashboard_links_to_saints_directory_and_details(): void
    {
        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Saints &amp; African Heritage', false);
        $response->assertSee('/saints');
        $response->assertSee('Learn &rarr;', false);
    }
}
