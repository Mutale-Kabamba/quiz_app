<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Deanery;
use App\Models\Lesson;
use App\Models\Parish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudyHubSeriesGroupingTest extends TestCase
{
    use RefreshDatabase;

    public function test_youth_study_hub_groups_lessons_by_series_and_limits_to_5(): void
    {
        $deanery = Deanery::create(['code' => 'LIV', 'name' => 'Livingstone Deanery']);
        $parish = Parish::create(['deanery_id' => $deanery->id, 'name' => 'St. Theresa Cathedral Parish', 'location' => 'Livingstone']);

        $youth = User::create([
            'parish_id' => $parish->id,
            'name' => 'Francis Mwape',
            'email' => 'francis@example.com',
            'phone' => '+260970000003',
            'password' => bcrypt('password'),
            'role' => 'youth',
            'status' => 'approved',
        ]);

        $cat = Category::create([
            'name' => 'Bible Study Methods & Exegesis',
            'code' => 'BIBLE_STUDY',
            'slug' => 'bible-study-methods-exegesis',
            'display_order' => 1,
            'is_active' => true,
        ]);

        // Create 7 series
        for ($s = 1; $s <= 7; $s++) {
            Lesson::create([
                'category_id' => $cat->id,
                'series_identifier' => "SERIES_{$s}",
                'title' => "Doctrinal Series {$s} (Part 1): Introduction",
                'slug' => "doctrinal-series-{$s}-part-1",
                'status' => 'published',
                'display_order' => $s * 2 - 1,
            ]);
            Lesson::create([
                'category_id' => $cat->id,
                'series_identifier' => "SERIES_{$s}",
                'title' => "Doctrinal Series {$s} (Part 2): Application",
                'slug' => "doctrinal-series-{$s}-part-2",
                'status' => 'published',
                'display_order' => $s * 2,
            ]);
        }

        $component = Livewire::actingAs($youth)
            ->test(\App\Livewire\StudyHub::class)
            ->assertSee('Formation Series (7)')
            ->assertSee('Showing 5 of 7 series')
            ->assertSee('View More Series (2 More Available)');

        // Toggle show more
        $component->call('toggleShowAllSeries')
            ->assertSet('showAllSeries', true)
            ->assertSee('Showing 7 of 7 series')
            ->assertSee('Show Top 5 Series Only');
    }
}
