<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Deanery;
use App\Models\FlashcardReview;
use App\Models\LessonProgress;
use App\Models\Parish;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DiocesanAnalyticsService
{
    /**
     * Compute top-level Diocesan command metrics
     */
    public function getDiocesanKpis(): array
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $sevenDaysAgo = $now->copy()->subDays(7)->toDateString();
        $thirtyDaysAgo = $now->copy()->subDays(30)->toDateString();

        // 1. Youth Users
        $totalYouth = User::where('role', 'youth')->count();
        $activeThisWeek = User::where('role', 'youth')
            ->where('last_activity_date', '>=', $sevenDaysAgo)
            ->count();
        $newRegistrationsMonth = User::where('role', 'youth')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->count();

        // 2. Parishes Health
        $totalParishes = Parish::count();
        $activeParishIds = User::where('role', 'youth')
            ->where('last_activity_date', '>=', $sevenDaysAgo)
            ->whereNotNull('parish_id')
            ->pluck('parish_id')
            ->unique();
        $activeParishesCount = $activeParishIds->count();
        $inactiveParishesCount = max(0, $totalParishes - $activeParishesCount);

        // 3. Learning & Formation Totals
        $lessonsCompleted = LessonProgress::where('is_completed', true)->count();
        $quizzesCompleted = QuizAttempt::count();
        $flashcardsReviewed = FlashcardReview::count();

        // 4. XP and Competition
        $totalXpAwarded = (int) User::where('role', 'youth')->sum('xp');
        $rankedSessions = QuizAttempt::where('mode', 'ranked')->count();

        // 5. Engagement DAU / WAU / MAU
        $dau = User::where('role', 'youth')->where('last_activity_date', $today)->count();
        $wau = $activeThisWeek;
        $mau = User::where('role', 'youth')->where('last_activity_date', '>=', $thirtyDaysAgo)->count();

        // 6. Average Accuracy
        $avgAccuracyQuery = QuizAttempt::select(
            DB::raw('AVG((correct_answers_count * 1.0) / CASE WHEN total_questions > 0 THEN total_questions ELSE 1 END) * 100 as accuracy')
        )->first();
        $avgAccuracy = (int) round($avgAccuracyQuery->accuracy ?? 0);

        return [
            'total_youth' => $totalYouth,
            'active_this_week' => $activeThisWeek,
            'new_registrations_month' => $newRegistrationsMonth,
            'total_parishes' => $totalParishes,
            'active_parishes' => $activeParishesCount,
            'inactive_parishes' => $inactiveParishesCount,
            'lessons_completed' => $lessonsCompleted,
            'quizzes_completed' => $quizzesCompleted,
            'flashcards_reviewed' => $flashcardsReviewed,
            'total_xp_awarded' => $totalXpAwarded,
            'ranked_sessions' => $rankedSessions,
            'dau' => $dau,
            'wau' => $wau,
            'mau' => $mau,
            'avg_accuracy' => $avgAccuracy,
        ];
    }

    /**
     * Get Deanery breakdown comparative ranking
     */
    public function getDeaneryPerformance(): array
    {
        $deaneries = Deanery::with('parishes')->get();
        $results = [];

        foreach ($deaneries as $deanery) {
            $parishIds = $deanery->parishes->pluck('id');
            $youthCount = User::whereIn('parish_id', $parishIds)->where('role', 'youth')->count();
            $activeYouth = User::whereIn('parish_id', $parishIds)
                ->where('role', 'youth')
                ->where('last_activity_date', '>=', now()->subDays(7)->toDateString())
                ->count();
            $totalXp = (int) User::whereIn('parish_id', $parishIds)->sum('xp');
            $quizzesCount = QuizAttempt::whereHas('user', fn($q) => $q->whereIn('parish_id', $parishIds))->count();

            $results[] = [
                'id' => $deanery->id,
                'name' => $deanery->name,
                'code' => $deanery->code,
                'parishes_count' => $deanery->parishes->count(),
                'total_youth' => $youthCount,
                'active_youth' => $activeYouth,
                'total_xp' => $totalXp,
                'quizzes_count' => $quizzesCount,
                'active_rate' => $youthCount > 0 ? (int) round(($activeYouth / $youthCount) * 100) : 0,
            ];
        }

        return collect($results)->sortByDesc('total_xp')->values()->all();
    }
}
