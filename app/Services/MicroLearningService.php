<?php

namespace App\Services;

use App\Models\Flashcard;
use App\Models\MicroLesson;
use App\Models\Question;
use App\Models\QuestionBankItem;
use App\Models\TaxonomyTopic;
use App\Models\User;
use App\Models\UserMicroLessonCompletion;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MicroLearningService
{
    public function __construct(
        protected GamificationService $gamificationService,
        protected StreakService $streakService
    ) {}

    /**
     * Retrieve the recommended 5-minute Micro Lesson for today.
     * When a lesson is completed, dynamically advances to the next uncompleted formation lesson.
     */
    public function getTodayMicroLesson(User $user): ?MicroLesson
    {
        $completedMicroLessonIds = UserMicroLessonCompletion::where('user_id', $user->id)
            ->pluck('micro_lesson_id')
            ->toArray();

        // 1. Primary: The user's active next uncompleted formation lesson in their track progression
        $continueLesson = app(LearningProgressService::class)->getContinueLearningLesson($user);
        if ($continueLesson) {
            $syncedMicro = MicroLesson::where(function ($q) use ($continueLesson) {
                $q->where('slug', $continueLesson->slug)->orWhere('title', $continueLesson->title);
            })->first();

            if ($syncedMicro && !in_array($syncedMicro->id, $completedMicroLessonIds)) {
                return $syncedMicro;
            }

            if (!$syncedMicro) {
                return MicroLesson::firstOrCreate(
                    ['slug' => $continueLesson->slug],
                    [
                        'category_id' => $continueLesson->category_id,
                        'title' => $continueLesson->title,
                        'hook_question' => $continueLesson->subheading ?: "What does Catholic doctrine teach about {$continueLesson->title}?",
                        'content_body' => is_array($continueLesson->content_sections) && isset($continueLesson->content_sections[0]['body'])
                            ? $continueLesson->content_sections[0]['body']
                            : (string) ($continueLesson->subheading ?? $continueLesson->title),
                        'takeaways' => $continueLesson->summary_takeaways ?: [$continueLesson->subheading],
                        'reference_citation' => $continueLesson->catechism_citations ?: ($continueLesson->scripture_citations ?: 'Holy Scripture & CCC'),
                        'read_time_minutes' => $continueLesson->estimated_read_minutes ?: 5,
                        'xp_reward' => 25,
                        'is_published' => ($continueLesson->status === 'published'),
                    ]
                );
            }
        }

        // 2. Secondary fallback: Any other uncompleted published MicroLesson
        $nextMicro = MicroLesson::where('is_published', true)
            ->whereNotIn('id', $completedMicroLessonIds)
            ->orderBy('id')
            ->first();

        if ($nextMicro) {
            return $nextMicro;
        }

        return null;
    }

    /**
     * Check if user completed the given micro lesson.
     */
    public function hasUserCompleted(User $user, MicroLesson $lesson): bool
    {
        return UserMicroLessonCompletion::where('user_id', $user->id)
            ->where('micro_lesson_id', $lesson->id)
            ->exists();
    }

    /**
     * Complete a micro lesson and award XP + update streak.
     */
    public function completeMicroLesson(User $user, MicroLesson $lesson, int $quizScore = 3, int $quizTotal = 3): UserMicroLessonCompletion
    {
        $completion = UserMicroLessonCompletion::updateOrCreate(
            [
                'user_id' => $user->id,
                'micro_lesson_id' => $lesson->id,
            ],
            [
                'quiz_score' => $quizScore,
                'quiz_total' => $quizTotal,
                'xp_earned' => $lesson->xp_reward ?? 25,
                'completed_at' => now(),
            ]
        );

        // Sync completion with main Lesson table if matching slug or title exists
        $matchingLesson = \App\Models\Lesson::where('slug', $lesson->slug)
            ->orWhere('title', $lesson->title)
            ->first();

        if ($matchingLesson) {
            \App\Models\LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $matchingLesson->id],
                [
                    'is_completed' => true,
                    'last_read_at' => now(),
                ]
            );
        }

        // Award authoritative XP
        $this->gamificationService->awardXp(
            $user,
            $lesson->xp_reward ?? 25,
            "Completed daily micro-lesson: {$lesson->title}",
            'micro_lesson_completed',
            (string) $lesson->id
        );

        // Record streak
        $this->streakService->recordFormationActivity($user);

        return $completion;
    }
}
