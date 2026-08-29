<?php

namespace App\Services;

use App\Models\LessonProgress;
use App\Models\Parish;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParishReportService
{
    /**
     * Generate structured Monthly Parish Youth Ministry Report
     */
    public function generateMonthlyReport(int $parishId, ?Carbon $month = null): array
    {
        $targetMonth = $month ?? Carbon::now();
        $startOfMonth = $targetMonth->copy()->startOfMonth();
        $endOfMonth = $targetMonth->copy()->endOfMonth();

        $parish = Parish::with('deanery')->find($parishId);

        // 1. Total Youth & New Registrations
        $totalYouth = User::where('parish_id', $parishId)->where('role', 'youth')->count();
        $newRegistrations = User::where('parish_id', $parishId)
            ->where('role', 'youth')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // 2. Active Youth in Month
        $activeYouth = User::where('parish_id', $parishId)
            ->where('role', 'youth')
            ->whereBetween('last_activity_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->count();

        // 3. Lessons Completed in Month
        $lessonsCompleted = LessonProgress::whereHas('user', fn($q) => $q->where('parish_id', $parishId))
            ->where('is_completed', true)
            ->whereBetween('last_read_at', [$startOfMonth, $endOfMonth])
            ->count();

        // 4. Quizzes & Accuracy
        $quizzes = QuizAttempt::whereHas('user', fn($q) => $q->where('parish_id', $parishId))
            ->whereBetween('completed_at', [$startOfMonth, $endOfMonth])
            ->get();

        $totalQuizzes = $quizzes->count();
        $avgScore = $quizzes->isNotEmpty() ? (int) round($quizzes->avg('score')) : 0;
        $avgAccuracy = $quizzes->isNotEmpty()
            ? (int) round($quizzes->avg(fn($a) => $a->total_questions > 0 ? ($a->correct_answers_count / $a->total_questions) * 100 : 0))
            : 0;

        // 5. Top Youth Performer in Month
        $topYouth = User::where('parish_id', $parishId)
            ->where('role', 'youth')
            ->orderByDesc('xp')
            ->first();

        // 6. Track Performance Analysis
        $analytics = app(ParishAnalyticsService::class)->getTrackPerformance($parishId);
        $strongestTrack = collect($analytics['tracks'])->sortByDesc('avg_accuracy')->first();
        $weakestTrack = collect($analytics['tracks'])->sortBy('avg_accuracy')->first();

        return [
            'parish_name' => $parish?->name ?? 'Parish',
            'deanery_name' => $parish?->deanery?->name ?? 'Deanery',
            'month_label' => $targetMonth->format('F Y'),
            'total_youth' => $totalYouth,
            'new_registrations' => $newRegistrations,
            'active_youth' => $activeYouth,
            'lessons_completed' => $lessonsCompleted,
            'total_quizzes' => $totalQuizzes,
            'avg_score' => $avgScore,
            'avg_accuracy' => $avgAccuracy,
            'top_youth_name' => $topYouth?->name ?? '—',
            'top_youth_xp' => $topYouth?->xp ?? 0,
            'strongest_track' => $strongestTrack['name'] ?? '—',
            'weakest_track' => $weakestTrack['name'] ?? '—',
            'recommendation' => $analytics['recommendation'],
        ];
    }
}
