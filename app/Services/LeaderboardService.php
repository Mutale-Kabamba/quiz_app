<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaderboardService
{
    /**
     * Get aggregated rankings with filters for individual youth
     */
    public function getRankings(string $scope, string $timeframe, ?int $categoryId, ?User $currentUser, int $limit = 50): array
    {
        $query = DB::table('quiz_attempts')
            ->join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->leftJoin('parishes', 'users.parish_id', '=', 'parishes.id')
            ->leftJoin('deaneries', 'parishes.deanery_id', '=', 'deaneries.id')
            ->where('users.status', 'approved')
            ->where('quiz_attempts.mode', 'ranked');

        // Apply Timeframe Filter
        $startDate = match ($timeframe) {
            'today' => Carbon::today(),
            'this_week' => Carbon::now()->startOfWeek(),
            'this_month' => Carbon::now()->startOfMonth(),
            default => null, // all_time
        };

        if ($startDate) {
            $query->where('quiz_attempts.completed_at', '>=', $startDate);
        }

        // Apply Category Filter
        if ($categoryId) {
            $query->where('quiz_attempts.category_id', $categoryId);
        }

        // Apply Hierarchical Scope Filter
        if ($scope === 'parish' && $currentUser && $currentUser->parish_id) {
            $query->where('users.parish_id', $currentUser->parish_id);
        } elseif ($scope === 'deanery' && $currentUser && $currentUser->parish?->deanery_id) {
            $query->where('parishes.deanery_id', $currentUser->parish->deanery_id);
        }

        $rankings = $query->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.level as user_level',
                'parishes.name as parish_name',
                'deaneries.name as deanery_name',
                DB::raw('SUM(quiz_attempts.score) as total_points'),
                DB::raw('COUNT(quiz_attempts.id) as attempts_count'),
                DB::raw('AVG((quiz_attempts.correct_answers_count * 1.0) / CASE WHEN quiz_attempts.total_questions > 0 THEN quiz_attempts.total_questions ELSE 1 END) * 100 as avg_accuracy')
            )
            ->groupBy('users.id', 'users.name', 'users.level', 'parishes.name', 'deaneries.name')
            ->orderByDesc('total_points')
            ->orderByDesc('avg_accuracy')
            ->limit($limit)
            ->get();

        $top3 = $rankings->take(3);
        $remaining = $rankings->slice(3);

        // Find Current User's Position & Proximity
        $userRank = null;
        $userPoints = 0;
        $pointsBehind = null;
        $aheadPlayerName = null;

        if ($currentUser) {
            $rankIndex = $rankings->search(fn($item) => $item->user_id === $currentUser->id);
            if ($rankIndex !== false) {
                $userRank = $rankIndex + 1;
                $userPoints = (int) $rankings[$rankIndex]->total_points;

                if ($rankIndex > 0) {
                    $aheadPlayer = $rankings[$rankIndex - 1];
                    $pointsBehind = (int) $aheadPlayer->total_points - $userPoints;
                    $aheadPlayerName = $aheadPlayer->user_name;
                }
            }
        }

        return [
            'top3' => $top3,
            'remaining' => $remaining,
            'userRank' => $userRank,
            'userPoints' => $userPoints,
            'pointsBehind' => $pointsBehind,
            'aheadPlayerName' => $aheadPlayerName,
        ];
    }

    /**
     * Get Deanery aggregate standings
     */
    public function getDeaneryRankings(): array
    {
        $deaneries = Deanery::with('parishes')->get();
        $results = [];

        foreach ($deaneries as $deanery) {
            $parishIds = $deanery->parishes->pluck('id');
            $youthCount = User::whereIn('parish_id', $parishIds)->where('role', 'youth')->count();
            $totalXp = (int) User::whereIn('parish_id', $parishIds)->where('role', 'youth')->sum('xp');
            $attemptsCount = QuizAttempt::whereHas('user', fn($q) => $q->whereIn('parish_id', $parishIds))->count();
            
            $stat = QuizAttempt::whereHas('user', fn($q) => $q->whereIn('parish_id', $parishIds))
                ->select(DB::raw('AVG((correct_answers_count * 1.0) / CASE WHEN total_questions > 0 THEN total_questions ELSE 1 END) * 100 as acc'))
                ->first();
            $avgAccuracy = (int) round($stat->acc ?? 0);

            $results[] = [
                'id' => $deanery->id,
                'name' => $deanery->name,
                'code' => $deanery->code,
                'parishes_count' => $deanery->parishes->count(),
                'youth_count' => $youthCount,
                'total_xp' => $totalXp,
                'attempts_count' => $attemptsCount,
                'avg_accuracy' => $avgAccuracy,
            ];
        }

        usort($results, fn($a, $b) => $b['total_xp'] <=> $a['total_xp']);
        return $results;
    }

    /**
     * Get Parish aggregate standings
     */
    public function getParishRankings(?int $deaneryId = null): array
    {
        $parishes = Parish::with('deanery')
            ->when($deaneryId, fn($q) => $q->where('deanery_id', $deaneryId))
            ->get();

        $results = [];
        foreach ($parishes as $parish) {
            $youthCount = User::where('parish_id', $parish->id)->where('role', 'youth')->count();
            $totalXp = (int) User::where('parish_id', $parish->id)->where('role', 'youth')->sum('xp');
            $attemptsCount = QuizAttempt::whereHas('user', fn($q) => $q->where('parish_id', $parish->id))->count();

            $stat = QuizAttempt::whereHas('user', fn($q) => $q->where('parish_id', $parish->id))
                ->select(DB::raw('AVG((correct_answers_count * 1.0) / CASE WHEN total_questions > 0 THEN total_questions ELSE 1 END) * 100 as acc'))
                ->first();
            $avgAccuracy = (int) round($stat->acc ?? 0);

            $results[] = [
                'id' => $parish->id,
                'name' => $parish->name,
                'code' => $parish->code,
                'deanery_name' => $parish->deanery?->name ?? 'Livingstone',
                'youth_count' => $youthCount,
                'total_xp' => $totalXp,
                'attempts_count' => $attemptsCount,
                'avg_accuracy' => $avgAccuracy,
            ];
        }

        usort($results, fn($a, $b) => $b['total_xp'] <=> $a['total_xp']);
        return $results;
    }
}
