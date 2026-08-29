<?php

namespace App\Services;

use App\Models\DailyChallenge;
use App\Models\LessonProgress;
use App\Models\Parish;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserChallengeParticipation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParishDashboardService
{
    /**
     * Compute comprehensive, parish-scoped KPI metrics
     */
    public function getParishKpis(int $parishId): array
    {
        $now = Carbon::now();
        $oneWeekAgo = $now->copy()->subDays(7);
        $twoWeeksAgo = $now->copy()->subDays(14);

        // 1. Total Youth in Parish
        $totalYouth = User::where('parish_id', $parishId)
            ->where('role', 'youth')
            ->count();

        // 2. Active This Week
        $activeThisWeek = User::where('parish_id', $parishId)
            ->where('role', 'youth')
            ->where('last_activity_date', '>=', $oneWeekAgo->toDateString())
            ->count();

        // 3. Lessons Completed by Parish Youth
        $lessonsCompleted = LessonProgress::whereHas('user', fn($q) => $q->where('parish_id', $parishId))
            ->where('is_completed', true)
            ->count();

        // 4. Quizzes Completed by Parish Youth
        $quizzesCompleted = QuizAttempt::whereHas('user', fn($q) => $q->where('parish_id', $parishId))->count();

        // 5. Total Parish XP
        $parishXp = (int) User::where('parish_id', $parishId)->sum('xp');

        // 6. Average Quiz Score & Accuracy
        $quizStats = QuizAttempt::whereHas('user', fn($q) => $q->where('parish_id', $parishId))
            ->select(
                DB::raw('AVG(score) as avg_score'),
                DB::raw('AVG((correct_answers_count * 1.0) / CASE WHEN total_questions > 0 THEN total_questions ELSE 1 END) * 100 as avg_accuracy')
            )
            ->first();

        $avgScore = (int) round($quizStats->avg_score ?? 0);
        $avgAccuracy = (int) round($quizStats->avg_accuracy ?? 0);

        // 7. Parish Health Evaluation
        $activeRate = $totalYouth > 0 ? ($activeThisWeek / $totalYouth) * 100 : 0;
        $healthStatus = match (true) {
            $activeRate >= 60 => ['badge' => '🟢 Strong', 'color' => 'success', 'rate' => (int) round($activeRate)],
            $activeRate >= 30 => ['badge' => '🟡 Moderate', 'color' => 'warning', 'rate' => (int) round($activeRate)],
            default => ['badge' => '🔴 Needs Attention', 'color' => 'danger', 'rate' => (int) round($activeRate)],
        };

        // 8. Actionable Alerts (Attention Required)
        $inactiveCount = User::where('parish_id', $parishId)
            ->where('role', 'youth')
            ->where(function ($q) use ($twoWeeksAgo) {
                $q->whereNull('last_activity_date')
                  ->orWhere('last_activity_date', '<', $twoWeeksAgo->toDateString());
            })
            ->count();

        $pendingApprovals = User::where('parish_id', $parishId)
            ->where('role', 'youth')
            ->where('status', 'pending')
            ->count();

        $todayChallenge = DailyChallenge::where('challenge_date', now()->toDateString())->first();
        $challengeCompletedToday = 0;
        if ($todayChallenge) {
            $challengeCompletedToday = UserChallengeParticipation::where('daily_challenge_id', $todayChallenge->id)
                ->whereHas('user', fn($q) => $q->where('parish_id', $parishId))
                ->count();
        }

        return [
            'total_youth' => $totalYouth,
            'active_this_week' => $activeThisWeek,
            'lessons_completed' => $lessonsCompleted,
            'quizzes_completed' => $quizzesCompleted,
            'parish_xp' => $parishXp,
            'avg_score' => $avgScore,
            'avg_accuracy' => $avgAccuracy,
            'health_status' => $healthStatus,
            'inactive_youth_count' => $inactiveCount,
            'pending_approvals_count' => $pendingApprovals,
            'challenge_completed_today' => $challengeCompletedToday,
        ];
    }
}
