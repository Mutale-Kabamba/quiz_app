<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;

class LearningProgressService
{
    /**
     * Mark a lesson as completed and award XP + streak
     */
    public function completeLesson(User $user, Lesson $lesson): array
    {
        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['is_completed' => false, 'is_bookmarked' => false]
        );

        $firstTime = !$progress->is_completed;

        $progress->update([
            'is_completed' => true,
            'last_read_at' => now(),
        ]);

        $xpResult = ['xp_gained' => 0];
        if ($firstTime) {
            $xpResult = app(GamificationService::class)->awardXp(
                $user,
                20,
                "Completed lesson: {$lesson->title}",
                'lesson_completed',
                (string) $lesson->id
            );
            app(StreakService::class)->recordFormationActivity($user);
        }

        return [
            'first_time' => $firstTime,
            'xp_result' => $xpResult,
            'next_lesson' => $this->getNextLesson($lesson),
        ];
    }

    /**
     * Toggle bookmark state for a lesson
     */
    public function toggleBookmark(User $user, Lesson $lesson): bool
    {
        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['is_completed' => false, 'is_bookmarked' => false]
        );

        $newBookmarkState = !$progress->is_bookmarked;
        $progress->update(['is_bookmarked' => $newBookmarkState]);

        return $newBookmarkState;
    }

    /**
     * Get overall catechetical category completion percentages
     */
    public function getCategoryProgress(User $user): array
    {
        $categories = Category::withCount(['lessons' => function ($q) {
            $q->where('status', 'published');
        }])->orderBy('display_order')->get();

        $completedLessonIds = $user->lessonProgress()
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $results = [];
        foreach ($categories as $cat) {
            $totalLessons = $cat->lessons_count;
            if ($totalLessons === 0) {
                $percentage = 0;
            } else {
                $completedInCategory = Lesson::where('category_id', $cat->id)
                    ->where('status', 'published')
                    ->whereIn('id', $completedLessonIds)
                    ->count();
                $percentage = (int) round(($completedInCategory / $totalLessons) * 100);
            }

            $results[] = [
                'category_id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'icon' => $cat->icon,
                'total_lessons' => $totalLessons,
                'percentage' => $percentage,
            ];
        }

        return $results;
    }

    /**
     * Get the immediate next recommended or in-progress lesson for a user
     */
    public function getContinueLearningLesson(User $user): ?Lesson
    {
        // 1. Check if user has read a lesson recently but not marked it completed
        $recentUncompleted = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', false)
            ->with('lesson')
            ->latest('last_read_at')
            ->first();

        if ($recentUncompleted && $recentUncompleted->lesson && $recentUncompleted->lesson->status === 'published') {
            return $recentUncompleted->lesson;
        }

        // 2. Find the first published uncompleted lesson in the primary categories
        $completedIds = $user->lessonProgress()
            ->where('is_completed', true)
            ->pluck('lesson_id');

        $nextLesson = Lesson::where('status', 'published')
            ->whereNotIn('id', $completedIds)
            ->orderBy('display_order')
            ->first();

        return $nextLesson ?? Lesson::where('status', 'published')->first();
    }

    /**
     * Find next sequential lesson in category
     */
    public function getNextLesson(Lesson $currentLesson): ?Lesson
    {
        $order = (int) ($currentLesson->display_order ?? 1);

        return Lesson::where('category_id', $currentLesson->category_id)
            ->where('status', 'published')
            ->where('display_order', '>', $order)
            ->orderBy('display_order')
            ->first();
    }
}
