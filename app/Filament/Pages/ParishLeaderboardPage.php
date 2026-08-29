<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Parish;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\LeaderboardService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class ParishLeaderboardPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-trophy';
    protected static string | UnitEnum | null $navigationGroup = 'Formation & Performance';
    protected static ?string $navigationLabel = 'Parish Leaderboard';
    protected string $view = 'filament.pages.parish-leaderboard-page';
    protected static ?string $title = 'Parish & Diocesan Ranks';

    public string $timeframe = 'this_week'; // today, this_week, this_month, all_time
    public ?int $categoryId = null;

    public function setTimeframe(string $timeframe): void
    {
        $this->timeframe = $timeframe;
    }

    public function setCategory(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        if (!$user || !$user->parish_id) {
            return ['youthRankings' => collect(), 'parishStandings' => collect(), 'myParish' => null];
        }

        $categories = Category::orderBy('display_order')->get();

        // 1. Parish Youth Leaderboard
        $youthData = app(LeaderboardService::class)->getRankings(
            'parish',
            $this->timeframe,
            $this->categoryId,
            $user
        );

        // 2. Public Parish Standing in Deanery & Diocese (Anonymous to other youth details)
        $startDate = match ($this->timeframe) {
            'today' => Carbon::today(),
            'this_week' => Carbon::now()->startOfWeek(),
            'this_month' => Carbon::now()->startOfMonth(),
            default => null,
        };

        $parishQuery = DB::table('quiz_attempts')
            ->join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->join('parishes', 'users.parish_id', '=', 'parishes.id')
            ->where('quiz_attempts.mode', 'ranked');

        if ($startDate) {
            $parishQuery->where('quiz_attempts.completed_at', '>=', $startDate);
        }

        if ($this->categoryId) {
            $parishQuery->where('quiz_attempts.category_id', $this->categoryId);
        }

        $parishStandings = $parishQuery->select(
                'parishes.id as parish_id',
                'parishes.name as parish_name',
                DB::raw('SUM(quiz_attempts.score) as total_parish_points'),
                DB::raw('COUNT(DISTINCT users.id) as participating_youth_count')
            )
            ->groupBy('parishes.id', 'parishes.name')
            ->orderByDesc('total_parish_points')
            ->get();

        $myParish = Parish::find($user->parish_id);

        return [
            'categories' => $categories,
            'top3' => $youthData['top3'],
            'remaining' => $youthData['remaining'],
            'parishStandings' => $parishStandings,
            'myParish' => $myParish,
        ];
    }
}
