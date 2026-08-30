<?php

namespace App\Livewire;

use App\Models\SaintProfile;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class SaintsHeritageHub extends Component
{
    public string $search = '';
    public string $activeFilter = 'all'; // all, african, youth, doctors, martyrs, fathers
    public array $counts = [];

    public function setFilter(string $filter)
    {
        $this->activeFilter = $filter;
    }

    public function getActiveCategoryTitleProperty(): string
    {
        if (!empty($this->search)) {
            return 'Search Results';
        }

        return match ($this->activeFilter) {
            'african' => 'African Heritage',
            'youth' => 'Youth Patrons',
            'martyrs' => 'Martyrs of Faith',
            'doctors' => 'Doctors of the Church',
            'fathers' => 'African Popes & Church Fathers',
            default => 'Universal & African Church',
        };
    }

    protected function applyCategoryFilter(Builder $query, string $filter): Builder
    {
        return match ($filter) {
            'african' => $query->where('is_african_heritage', true),
            'youth' => $query->where(function (Builder $q) {
                $q->whereIn('slug', [
                    'st-charles-lwanga',
                    'st-kizito',
                    'st-theresa-of-lisieux',
                    'blessed-isidore-bakanja',
                    'blessed-anuarite-nengapeta',
                    'blessed-daudi-okelo-jildo-irwa',
                    'st-aloysius-gonzaga',
                    'st-dominic-savio',
                    'st-maria-goretti',
                    'blessed-carlo-acutis',
                    'st-tarcisius',
                    'st-agnes-of-rome',
                    'st-joan-of-arc',
                ])
                ->orWhere('title_designation', 'like', '%Youth%')
                ->orWhere('title_designation', 'like', '%Student%')
                ->orWhere('title_designation', 'like', '%Children%')
                ->orWhere('title_designation', 'like', '%Girl%')
                ->orWhere('patronages', 'like', '%Youth%')
                ->orWhere('patronages', 'like', '%Student%')
                ->orWhere('patronages', 'like', '%Children%');
            }),
            'martyrs' => $query->where(function (Builder $q) {
                $q->whereIn('slug', [
                    'st-charles-lwanga',
                    'st-kizito',
                    'st-mathias-mulumba',
                    'blessed-isidore-bakanja',
                    'blessed-anuarite-nengapeta',
                    'blessed-daudi-okelo-jildo-irwa',
                    'blessed-benedict-daswa',
                    'st-perpetua-and-felicity',
                    'st-cyprian-of-carthage',
                    'pope-st-victor-i',
                    'st-maria-goretti',
                    'st-tarcisius',
                    'st-agnes-of-rome',
                    'st-joan-of-arc',
                    'st-stephen',
                    'st-lawrence',
                ])
                ->orWhere('title_designation', 'like', '%Martyr%');
            }),
            'doctors' => $query->where(function (Builder $q) {
                $q->whereIn('slug', [
                    'st-theresa-of-lisieux',
                    'st-augustine-of-hippo',
                    'st-athanasius',
                    'st-cyril-of-alexandria',
                    'st-thomas-aquinas',
                    'st-catherine-of-siena',
                    'st-teresa-of-avila',
                    'st-john-chrysostom',
                    'st-jerome',
                    'st-francis-de-sales',
                ])
                ->orWhere('title_designation', 'like', '%Doctor%');
            }),
            'fathers' => $query->where(function (Builder $q) {
                $q->whereIn('slug', [
                    'st-augustine-of-hippo',
                    'st-athanasius',
                    'st-cyril-of-alexandria',
                    'st-anthony-of-egypt',
                    'st-pachomius-of-egypt',
                    'st-moses-the-black',
                    'st-cyprian-of-carthage',
                    'st-clement-of-alexandria',
                    'pope-st-victor-i',
                    'pope-st-miltiades',
                    'pope-st-gelasius-i',
                ])
                ->orWhere(function ($sub) {
                    $sub->where('is_african_heritage', true)
                        ->where(function ($deep) {
                            $deep->where('title_designation', 'like', '%Pope%')
                                 ->orWhere('title_designation', 'like', '%Father%')
                                 ->orWhere('title_designation', 'like', '%Patriarch%')
                                 ->orWhere('title_designation', 'like', '%Monasticism%');
                        });
                });
            }),
            default => $query,
        };
    }

    public function render()
    {
        $query = SaintProfile::query();

        // 1. Search Query
        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('title_designation', 'like', $term)
                    ->orWhere('country_region', 'like', $term)
                    ->orWhere('biography', 'like', $term);
            });
        }

        // 2. Apply Active Category Filter
        $this->applyCategoryFilter($query, $this->activeFilter);

        // Order: Cathedral Patroness first, then African Heritage, then alphabetical
        $saints = $query->orderByRaw("CASE WHEN slug = 'st-theresa-of-lisieux' THEN 1 WHEN is_african_heritage = 1 THEN 2 ELSE 3 END")
            ->orderBy('name')
            ->get();

        // Compute counts per category
        $this->counts = [
            'all' => SaintProfile::count(),
            'african' => $this->applyCategoryFilter(SaintProfile::query(), 'african')->count(),
            'youth' => $this->applyCategoryFilter(SaintProfile::query(), 'youth')->count(),
            'martyrs' => $this->applyCategoryFilter(SaintProfile::query(), 'martyrs')->count(),
            'doctors' => $this->applyCategoryFilter(SaintProfile::query(), 'doctors')->count(),
            'fathers' => $this->applyCategoryFilter(SaintProfile::query(), 'fathers')->count(),
        ];

        return view('livewire.saints-heritage-hub', [
            'saints' => $saints,
            'counts' => $this->counts,
            'totalCount' => $this->counts['all'],
            'africanCount' => $this->counts['african'],
        ])->layout('components.layouts.app', ['title' => 'Saints & African Heritage • Livingstone Diocese']);
    }
}
