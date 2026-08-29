<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Parish;
use App\Services\LeaderboardService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LeaderboardView extends Component
{
    public string $scope = 'diocese'; // 'deanery', 'parish', 'youth', 'diocese'
    public string $timeframe = 'this_week'; // 'today', 'this_week', 'this_month', 'all_time'
    public ?int $categoryId = null; // null for all categories

    public function mount()
    {
        $user = Auth::user();
        if ($user?->isSuperAdmin()) {
            $this->scope = 'deanery';
        } elseif ($user?->isChairperson()) {
            $this->scope = 'parish';
        } else {
            $this->scope = 'diocese';
        }
    }

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
        if (!$currentUser) {
            return redirect()->to('/login');
        }

        $categories = Category::orderBy('display_order')->get();
        $leaderboardService = app(LeaderboardService::class);

        // =========================================================================
        // A. SUPER ADMIN MULTI-TIER RANKINGS
        // =========================================================================
        if ($currentUser->isSuperAdmin()) {
            $deaneryStandings = $this->scope === 'deanery' ? $leaderboardService->getDeaneryRankings() : [];
            $parishStandings = $this->scope === 'parish' ? $leaderboardService->getParishRankings() : [];
            $youthStandings = $this->scope === 'youth' ? $leaderboardService->getRankings('diocese', $this->timeframe, $this->categoryId, $currentUser) : null;

            return view('livewire.leaderboard-view', [
                'currentUser' => $currentUser,
                'categories' => $categories,
                'deaneryStandings' => $deaneryStandings,
                'parishStandings' => $parishStandings,
                'youthStandings' => $youthStandings,
            ])->layout('components.layouts.app', ['title' => 'Diocesan Rankings & Standings • Livingstone Diocese']);
        }

        // =========================================================================
        // B. PARISH ADMIN (CHAIRPERSON) RANKINGS
        // =========================================================================
        if ($currentUser->isChairperson()) {
            $parish = $currentUser->parish ?? Parish::first();
            $parishYouthRankings = $this->scope === 'parish' ? $leaderboardService->getRankings('parish', $this->timeframe, $this->categoryId, $currentUser) : null;
            $deaneryParishStandings = $this->scope === 'deanery' ? $leaderboardService->getParishRankings($parish->deanery_id) : [];

            return view('livewire.leaderboard-view', [
                'currentUser' => $currentUser,
                'parish' => $parish,
                'categories' => $categories,
                'parishYouthRankings' => $parishYouthRankings,
                'deaneryParishStandings' => $deaneryParishStandings,
            ])->layout('components.layouts.app', ['title' => "Parish Standings • {$parish->name}"]);
        }

        // =========================================================================
        // C. YOUTH LEARNER LEADERBOARD
        // =========================================================================
        $data = $leaderboardService->getRankings(
            $this->scope,
            $this->timeframe,
            $this->categoryId,
            $currentUser
        );

        return view('livewire.leaderboard-view', [
            'currentUser' => $currentUser,
            'categories' => $categories,
            'top3' => $data['top3'],
            'remaining' => $data['remaining'],
            'userRank' => $data['userRank'],
            'userPoints' => $data['userPoints'],
            'pointsBehind' => $data['pointsBehind'],
            'aheadPlayerName' => $data['aheadPlayerName'],
        ])->layout('components.layouts.app', ['title' => 'Leaderboards • Diocese of Livingstone']);
    }
}
