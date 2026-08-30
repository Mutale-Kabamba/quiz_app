<?php

namespace App\Livewire;

use App\Models\SaintProfile;
use Livewire\Component;

class SaintDetail extends Component
{
    public SaintProfile $saint;
    public bool $copiedPrayer = false;

    public function mount(string $slug)
    {
        $this->saint = SaintProfile::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        $otherSaints = SaintProfile::where('id', '!=', $this->saint->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('livewire.saint-detail', [
            'saint' => $this->saint,
            'otherSaints' => $otherSaints,
        ])->layout('components.layouts.app', ['title' => "{$this->saint->name} • Saints & African Heritage"]);
    }
}
