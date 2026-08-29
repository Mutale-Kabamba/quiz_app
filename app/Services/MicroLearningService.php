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
     */
    public function getTodayMicroLesson(User $user): ?MicroLesson
    {
        // 1. Check if user already has an uncompleted micro lesson for today
        $lesson = MicroLesson::where('is_published', true)->first();

        if (!$lesson) {
            // Seed a starter micro lesson if none exists
            $topic = TaxonomyTopic::first();
            $flashcards = Flashcard::limit(3)->pluck('id')->toArray();
            $questions = Question::limit(3)->pluck('id')->toArray();

            $lesson = MicroLesson::create([
                'topic_id' => $topic?->id,
                'title' => 'The Four Marks of the Church',
                'slug' => 'four-marks-of-the-church',
                'hook_question' => 'Why do we profess One, Holy, Catholic, and Apostolic Church in the Creed?',
                'content_body' => 'In the Nicene Creed, we profess four essential marks of the Church established by Jesus Christ: One (united in faith and baptism), Holy (sanctified by Christ and the Holy Spirit), Catholic (universal across all nations and times), and Apostolic (built upon the foundation of the Apostles and their successors).',
                'takeaways' => [
                    'The Church is One in her source, founder, and soul.',
                    'The Church is Holy because Christ loved her and gave Himself up for her.',
                    'The Church is Catholic because she is universal and sent to all peoples.',
                    'The Church is Apostolic through apostolic succession and apostolic faith.',
                ],
                'flashcard_ids' => $flashcards,
                'question_ids' => $questions,
                'reference_citation' => 'CCC 811-870, Matthew 16:18-19',
                'read_time_minutes' => 4,
                'xp_reward' => 40,
                'is_published' => true,
            ]);
        }

        return $lesson;
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
                'xp_earned' => $lesson->xp_reward,
                'completed_at' => now(),
            ]
        );

        // Award authoritative XP
        $this->gamificationService->awardXp(
            $user,
            $lesson->xp_reward,
            "Completed 5-Min Formation: {$lesson->title}",
            'micro_lesson_completion',
            $lesson->id
        );

        // Advance formation streak
        $this->streakService->recordFormationActivity($user);

        return $completion;
    }
}
