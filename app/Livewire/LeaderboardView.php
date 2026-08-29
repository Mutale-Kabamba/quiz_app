<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LeaderboardView extends Component
{
    public string $scope = 'diocese'; // 'parish', 'deanery', 'diocese'
    public ?int $categoryId = null; // null for all categories

    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    public function setCategory(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function render()
    {
        $currentUser = Auth::user();
        $categories = Category::orderBy('display_order')->get();

        // Build Leaderboard Aggregation Query
        $query = DB::table('quiz_attempts')
            ->join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->leftJoin('parishes', 'users.parish_id', '=', 'parishes.id')
            ->leftJoin('deaneries', 'parishes.deanery_id', '=', 'deaneries.id')
            ->where('users.status', 'approved') // Only approved youth compete in rankings
            ->where('quiz_attempts.mode', 'ranked');

        // Apply Category Filter
        if ($this->categoryId) {
            $query->where('quiz_attempts.category_id', $this->categoryId);
        }

        // Apply Hierarchical Scope Filter
        if ($this->scope === 'parish' && $currentUser && $currentUser->parish_id) {
            $query->where('users.parish_id', $currentUser->parish_id);
        } elseif ($this->scope === 'deanery' && $currentUser && $currentUser->parish?->deanery_id) {
            $query->where('parishes.deanery_id', $currentUser->parish->deanery_id);
        }

        // Aggregate User Total Scores
        $rankings = $query->select(
                'users.id as user_id',
                'users.name as user_name',
                'parishes.name as parish_name',
                'deaneries.name as deanery_name',
                DB::raw('SUM(quiz_attempts.score) as total_points'),
                DB::raw('COUNT(quiz_attempts.id) as attempts_count')
            )
            ->groupBy('users.id', 'users.name', 'parishes.name', 'deaneries.name')
            ->orderByDesc('total_points')
            ->limit(50)
            ->get();

        // Top 3 Podium vs Remaining Ranks
        $top3 = $rankings->take(3);
        $remaining = $rankings->slice(3);

        // Find Current User's Rank
        $userRank = null;
        $userPoints = 0;
        if ($currentUser) {
            $rankIndex = $rankings->search(fn($item) => $item->user_id === $currentUser->id);
            if ($rankIndex !== false) {
                $userRank = $rankIndex + 1;
                $userPoints = $rankings[$rankIndex]->total_points;
            }
        }

        return view('livewire.leaderboard-view', [
            'categories' => $categories,
            'top3' => $top3,
            'remaining' => $remaining,
            'userRank' => $userRank,
            'userPoints' => $userPoints,
            'currentUser' => $currentUser,
        ])->layout('components.layouts.app', ['title' => 'Leaderboards • Diocese of Livingstone']);
    }
}
