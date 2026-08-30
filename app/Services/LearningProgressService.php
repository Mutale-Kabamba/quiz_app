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

        // Sync with MicroLesson completion if matching slug or title exists
        $matchingMicro = \App\Models\MicroLesson::where('slug', $lesson->slug)
            ->orWhere('title', $lesson->title)
            ->first();

        if ($matchingMicro) {
            \App\Models\UserMicroLessonCompletion::firstOrCreate(
                ['user_id' => $user->id, 'micro_lesson_id' => $matchingMicro->id],
                [
                    'quiz_score' => 3,
                    'quiz_total' => 3,
                    'xp_earned' => $matchingMicro->xp_reward ?? 25,
                    'completed_at' => now(),
                ]
            );
        }

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
     * Get an active uncompleted lesson that the user has started reading
     */
    public function getOngoingLesson(User $user): ?Lesson
    {
        $recentUncompleted = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', false)
            ->whereNotNull('last_read_at')
            ->with('lesson.category')
            ->latest('last_read_at')
            ->first();

        if ($recentUncompleted && $recentUncompleted->lesson && $recentUncompleted->lesson->status === 'published') {
            return $recentUncompleted->lesson;
        }

        return null;
    }

    /**
     * Get the immediate next recommended or in-progress lesson for a user
     */
    public function getContinueLearningLesson(User $user): ?Lesson
    {
        // 1. Check if user has read a lesson recently but not marked it completed
        $ongoing = $this->getOngoingLesson($user);
        if ($ongoing) {
            return $ongoing;
        }

        // 2. Check the user's latest completed lesson and find the NEXT sequential lesson
        $latestCompleted = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->with('lesson')
            ->latest('updated_at')
            ->first();

        if ($latestCompleted && $latestCompleted->lesson) {
            $nextCandidate = $this->getNextLesson($latestCompleted->lesson);
            if ($nextCandidate) {
                $alreadyDone = LessonProgress::where('user_id', $user->id)
                    ->where('lesson_id', $nextCandidate->id)
                    ->where('is_completed', true)
                    ->exists();

                if (!$alreadyDone) {
                    return $nextCandidate;
                }
            }
        }

        // 3. Find the first published uncompleted lesson across all categories/tracks
        $completedIds = $user->lessonProgress()
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $nextLesson = Lesson::where('status', 'published')
            ->whereNotIn('id', $completedIds)
            ->orderBy('display_order')
            ->orderBy('id')
            ->first();

        return $nextLesson ?? Lesson::where('status', 'published')->orderBy('display_order')->first();
    }

    /**
     * Find next sequential lesson in category, series (e.g. Part 1 -> Part 2), or next track
     */
    public function getNextLesson(Lesson|\App\Models\MicroLesson $currentLesson): ?Lesson
    {
        if ($currentLesson instanceof \App\Models\MicroLesson) {
            $matched = Lesson::where('slug', $currentLesson->slug)
                ->orWhere('title', $currentLesson->title)
                ->first();
            if ($matched) {
                $currentLesson = $matched;
            } else {
                $nextMicro = \App\Models\MicroLesson::where('category_id', $currentLesson->category_id)
                    ->where('is_published', true)
                    ->where('id', '!=', $currentLesson->id)
                    ->orderBy('id')
                    ->first();
                if ($nextMicro) {
                    return Lesson::where('slug', $nextMicro->slug)->first()
                        ?? Lesson::where('category_id', $nextMicro->category_id)->first();
                }
                return Lesson::where('status', 'published')->first();
            }
        }

        $categoryId = $currentLesson->category_id;
        $order = (int) ($currentLesson->display_order ?? 0);

        // Strategy 0 (Top Priority): Explicit series identifier progressive linking
        if (!empty($currentLesson->series_identifier)) {
            $nextInSeries = Lesson::where('series_identifier', $currentLesson->series_identifier)
                ->where('status', 'published')
                ->where('id', '!=', $currentLesson->id)
                ->where(function ($q) use ($currentLesson) {
                    if ($currentLesson->series_order) {
                        $q->where('series_order', '>', $currentLesson->series_order);
                    }
                })
                ->orderBy('series_order')
                ->orderBy('id')
                ->first();

            if ($nextInSeries) {
                return $nextInSeries;
            }
        }

        // Strategy 1: Next by higher display_order in the same category
        if ($order > 0) {
            $nextByOrder = Lesson::where('category_id', $categoryId)
                ->where('status', 'published')
                ->where('display_order', '>', $order)
                ->orderBy('display_order')
                ->orderBy('id')
                ->first();

            if ($nextByOrder) {
                return $nextByOrder;
            }
        }

        // Strategy 2: Natural Part / Lesson / Series progression in title (e.g. Part 1 -> Part 2, Lesson 1 -> Lesson 2, #1 -> #2)
        if (preg_match('/(?:part|lesson|#)\s*([0-9]+)/i', $currentLesson->title, $matches)) {
            $currentNum = (int) $matches[1];
            $nextNum = $currentNum + 1;

            $nextByPart = Lesson::where('category_id', $categoryId)
                ->where('status', 'published')
                ->where('id', '!=', $currentLesson->id)
                ->where(function ($q) use ($nextNum) {
                    $q->where('title', 'LIKE', "%Part {$nextNum}%")
                      ->orWhere('title', 'LIKE', "%Part {$nextNum}:%")
                      ->orWhere('title', 'LIKE', "%(Part {$nextNum})%")
                      ->orWhere('title', 'LIKE', "%Part ({$nextNum})%")
                      ->orWhere('title', 'LIKE', "%Lesson {$nextNum}%")
                      ->orWhere('title', 'LIKE', "%#{$nextNum}%");
                })
                ->orderBy('id')
                ->first();

            if ($nextByPart) {
                return $nextByPart;
            }
        }

        // Strategy 3: Next created lesson or higher ID in the same category
        $nextById = Lesson::where('category_id', $categoryId)
            ->where('status', 'published')
            ->where('id', '>', $currentLesson->id)
            ->orderBy('id')
            ->first();

        if ($nextById) {
            return $nextById;
        }

        // Strategy 4: If track finished, link to the first lesson in the next formation track
        $currentCat = Category::find($categoryId);
        $nextCategory = Category::where('display_order', '>', $currentCat?->display_order ?? 0)
            ->orderBy('display_order')
            ->first();

        if ($nextCategory) {
            $firstInNextCat = Lesson::where('category_id', $nextCategory->id)
                ->where('status', 'published')
                ->orderBy('display_order')
                ->orderBy('id')
                ->first();

            if ($firstInNextCat) {
                return $firstInNextCat;
            }
        }

        return null;
    }
}
