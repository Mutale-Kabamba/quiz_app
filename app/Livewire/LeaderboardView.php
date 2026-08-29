<?php

namespace App\Livewire;

use App\Models\Category;
use App\Services\LeaderboardService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LeaderboardView extends Component
{
    public string $scope = 'diocese'; // 'parish', 'deanery', 'diocese'
    public string $timeframe = 'this_week'; // 'today', 'this_week', 'this_month', 'all_time'
    public ?int $categoryId = null; // null for all categories

    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    public function setTimeframe(string $timeframe): void
    {
        $this->timeframe = $timeframe;
    }

    public function setCategory(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function render()
    {
        $currentUser = Auth::user();
        $categories = Category::orderBy('display_order')->get();

        $data = app(LeaderboardService::class)->getRankings(
            $this->scope,
            $this->timeframe,
            $this->categoryId,
            $currentUser
        );

        return view('livewire.leaderboard-view', [
            'categories' => $categories,
            'top3' => $data['top3'],
            'remaining' => $data['remaining'],
            'userRank' => $data['userRank'],
            'userPoints' => $data['userPoints'],
            'pointsBehind' => $data['pointsBehind'],
            'aheadPlayerName' => $data['aheadPlayerName'],
            'currentUser' => $currentUser,
        ])->layout('components.layouts.app', ['title' => 'Leaderboards • Diocese of Livingstone']);
    }
}
