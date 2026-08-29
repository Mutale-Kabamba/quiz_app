<?php

namespace App\Services;

use App\Models\Category;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Support\Facades\DB;

class ParishAnalyticsService
{
    /**
     * Compute catechetical track performance breakdown for a parish
     */
    public function getTrackPerformance(int $parishId): array
    {
        $categories = Category::orderBy('display_order')->get();
        $tracks = [];

        foreach ($categories as $cat) {
            $totalLessons = $cat->lessons()->where('status', 'published')->count();
            
            $completedLessons = LessonProgress::whereHas('user', fn($q) => $q->where('parish_id', $parishId))
                ->whereHas('lesson', fn($q) => $q->where('category_id', $cat->id))
                ->where('is_completed', true)
                ->count();

            $quizAttempts = QuizAttempt::whereHas('user', fn($q) => $q->where('parish_id', $parishId))
                ->where('category_id', $cat->id)
                ->get();

            $participatingYouth = $quizAttempts->pluck('user_id')->unique()->count();
            $avgScore = $quizAttempts->isNotEmpty() ? (int) round($quizAttempts->avg('score')) : 0;
            $avgAccuracy = $quizAttempts->isNotEmpty() 
                ? (int) round($quizAttempts->avg(fn($a) => $a->total_questions > 0 ? ($a->correct_answers_count / $a->total_questions) * 100 : 0))
                : 0;

            $tracks[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'icon' => $cat->icon,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'participating_youth' => $participatingYouth,
                'avg_score' => $avgScore,
                'avg_accuracy' => $avgAccuracy,
            ];
        }

        // Identify Weakest Track for Recommendation
        $weakestTrack = collect($tracks)->sortBy('avg_accuracy')->first();
        $recommendation = null;
        if ($weakestTrack && $weakestTrack['avg_accuracy'] > 0 && $weakestTrack['avg_accuracy'] < 70) {
            $recommendation = "Youth scored lowest in {$weakestTrack['name']} ({$weakestTrack['avg_accuracy']}% accuracy). Consider organizing a targeted parish study session or quiz practice on this track.";
        }

        return [
            'tracks' => $tracks,
            'weakest_track' => $weakestTrack,
            'recommendation' => $recommendation,
        ];
    }
}
