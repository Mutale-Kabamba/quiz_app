<?php

namespace App\Services;

use App\Models\Category;
use App\Models\FlashcardReview;
use App\Models\LessonProgress;
use App\Models\ParishTransfer;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Hash;

class ParishYouthService
{
    /**
     * Register a new youth strictly under the logged-in Parish Admin's parish
     */
    public function createYouth(User $admin, array $data): User
    {
        if (!$admin->parish_id) {
            throw new AuthorizationException("Administrator does not have an assigned parish.");
        }

        return User::create([
            'parish_id' => $admin->parish_id, // Immutably server-assigned
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password'] ?? 'password123'),
            'role' => 'youth',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'xp' => 0,
            'level' => 1,
            'current_streak' => 0,
            'longest_streak' => 0,
        ]);
    }

    /**
     * Suspend a youth member strictly within the admin's parish
     */
    public function suspendYouth(User $admin, User $youth, string $reason = ''): void
    {
        if (!$admin->isSuperAdmin() && $youth->parish_id !== $admin->parish_id) {
            throw new AuthorizationException("Cannot manage youth from another parish.");
        }

        $youth->update([
            'status' => 'rejected',
            'rejection_reason' => $reason ?: 'Suspended by Parish Administrator.',
        ]);
    }

    /**
     * Reactivate an approved youth
     */
    public function reactivateYouth(User $admin, User $youth): void
    {
        if (!$admin->isSuperAdmin() && $youth->parish_id !== $admin->parish_id) {
            throw new AuthorizationException("Cannot manage youth from another parish.");
        }

        $youth->update([
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Submit a transfer request to move a youth to another parish for Super Admin approval
     */
    public function requestTransfer(User $admin, User $youth, int $toParishId, string $reason): ParishTransfer
    {
        if (!$admin->isSuperAdmin() && $youth->parish_id !== $admin->parish_id) {
            throw new AuthorizationException("Cannot transfer youth from another parish.");
        }

        return ParishTransfer::create([
            'user_id' => $youth->id,
            'from_parish_id' => $youth->parish_id,
            'to_parish_id' => $toParishId,
            'requested_by' => $admin->id,
            'reason' => $reason,
            'status' => 'pending',
        ]);
    }

    /**
     * Get detailed learning and competition breakdown for a youth profile
     */
    public function getYouthProfileData(User $youth): array
    {
        $categories = Category::orderBy('display_order')->get();
        $trackProgress = [];

        foreach ($categories as $cat) {
            $totalLessons = $cat->lessons()->where('status', 'published')->count();
            $completedLessons = LessonProgress::where('user_id', $youth->id)
                ->whereHas('lesson', fn($q) => $q->where('category_id', $cat->id))
                ->where('is_completed', true)
                ->count();

            $quizAttempts = QuizAttempt::where('user_id', $youth->id)
                ->where('category_id', $cat->id)
                ->get();

            $quizAvgAccuracy = $quizAttempts->isNotEmpty()
                ? (int) round($quizAttempts->avg(fn($a) => ($a->total_questions > 0 ? ($a->correct_answers_count / $a->total_questions) * 100 : 0)))
                : 0;

            $trackProgress[] = [
                'category_name' => $cat->name,
                'icon' => $cat->icon,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'completion_rate' => $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0,
                'quiz_accuracy' => $quizAvgAccuracy,
                'quizzes_count' => $quizAttempts->count(),
            ];
        }

        $totalQuizzes = QuizAttempt::where('user_id', $youth->id)->count();
        $totalLessonsCompleted = LessonProgress::where('user_id', $youth->id)->where('is_completed', true)->count();
        $masteredFlashcards = FlashcardReview::where('user_id', $youth->id)->where('rating', 3)->count();
        $unlockedBadges = $youth->achievements()->with('achievement')->get()->pluck('achievement');

        // Parish Rank
        $parishRank = User::where('parish_id', $youth->parish_id)
            ->where('role', 'youth')
            ->where('xp', '>', $youth->xp)
            ->count() + 1;

        return [
            'total_quizzes' => $totalQuizzes,
            'total_lessons_completed' => $totalLessonsCompleted,
            'mastered_flashcards' => $masteredFlashcards,
            'unlocked_badges' => $unlockedBadges,
            'track_progress' => $trackProgress,
            'parish_rank' => $parishRank,
        ];
    }
}
