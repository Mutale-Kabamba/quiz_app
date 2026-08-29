<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Lesson;
use App\Models\QuizAttemptAnswer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LearningIntelligenceService
{
    /**
     * Analyze recent quiz attempts to extract Mastered vs Weak topics
     */
    public function analyzePerformance(User $user): array
    {
        $recentAnswerStats = QuizAttemptAnswer::whereHas('attempt', fn($q) => $q->where('user_id', $user->id))
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                'category_id',
                DB::raw('COUNT(*) as total_answers'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->groupBy('category_id')
            ->get();

        $masteredCategories = [];
        $weakCategories = [];

        foreach ($recentAnswerStats as $stat) {
            $category = Category::find($stat->category_id);
            if (!$category) {
                continue;
            }

            $accuracy = $stat->total_answers > 0 ? ($stat->correct_answers / $stat->total_answers) * 100 : 0;

            if ($accuracy >= 75 && $stat->total_answers >= 3) {
                $masteredCategories[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'accuracy' => (int) round($accuracy),
                    'icon' => $category->icon,
                ];
            } elseif ($accuracy < 65 || ($stat->total_answers - $stat->correct_answers) >= 2) {
                $weakCategories[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'accuracy' => (int) round($accuracy),
                    'icon' => $category->icon,
                ];
            }
        }

        return [
            'mastered' => $masteredCategories,
            'weak' => $weakCategories,
        ];
    }

    /**
     * Get tailored smart learning recommendation based on recent performance
     */
    public function getRecommendations(User $user): array
    {
        $analysis = $this->analyzePerformance($user);
        $weak = $analysis['weak'];

        $recommendations = [];

        if (!empty($weak)) {
            $targetCategoryId = $weak[0]['id'];
            $targetCategoryName = $weak[0]['name'];

            // Find an uncompleted lesson in this weak category
            $completedLessonIds = $user->lessonProgress()->where('is_completed', true)->pluck('lesson_id');
            $recommendedLesson = Lesson::where('category_id', $targetCategoryId)
                ->where('status', 'published')
                ->whereNotIn('id', $completedLessonIds)
                ->first();

            $recommendations[] = [
                'type' => 'weak_area',
                'title' => "Strengthen {$targetCategoryName}",
                'description' => "You recently missed questions on {$targetCategoryName}. Reviewing this track will boost your rally score!",
                'action_label' => $recommendedLesson ? 'Study Lesson' : 'Practice Flashcards',
                'action_url' => $recommendedLesson ? "/lesson/{$recommendedLesson->id}" : "/flashcards/{$targetCategoryId}",
                'category_id' => $targetCategoryId,
            ];
        }

        // Add daily formation prompt
        $continueLesson = app(LearningProgressService::class)->getContinueLearningLesson($user);
        if ($continueLesson) {
            $recommendations[] = [
                'type' => 'continue_learning',
                'title' => $continueLesson->title,
                'description' => $continueLesson->subheading ?? 'Continue your catechetical formation journey.',
                'action_label' => 'Continue Lesson',
                'action_url' => "/lesson/{$continueLesson->id}",
                'category_id' => $continueLesson->category_id,
            ];
        }

        return $recommendations;
    }
}
