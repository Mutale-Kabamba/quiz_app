<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\FlashcardReview;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->to('/login');
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $gamificationService = app(GamificationService::class);

        // Learning Metrics
        $totalScore = QuizAttempt::where('user_id', $user->id)->sum('score');
        $totalQuizzes = QuizAttempt::where('user_id', $user->id)->count();
        $completedLessonsCount = LessonProgress::where('user_id', $user->id)->where('is_completed', true)->count();
        $masteredFlashcardsCount = FlashcardReview::where('user_id', $user->id)->where('rating', 3)->count();

        // Level & XP Progress
        $currentLevel = $user->level ?? 1;
        $currentXp = $user->xp ?? 0;
        $currentBaseline = $gamificationService->getCurrentLevelBaseline($currentLevel);
        $nextThreshold = $gamificationService->getNextLevelThreshold($currentLevel);
        $levelXpSpan = max(1, $nextThreshold - $currentBaseline);
        $levelProgressPercentage = min(100, (int) round((($currentXp - $currentBaseline) / $levelXpSpan) * 100));

        // Achievements (Unlocked vs Locked)
        $unlockedAchievementIds = $user->achievements()->pluck('achievement_id')->toArray();
        $allAchievements = Achievement::all();

        // Bookmarked Lessons
        $bookmarkedLessons = LessonProgress::where('user_id', $user->id)
            ->where('is_bookmarked', true)
            ->with('lesson.category')
            ->get()
            ->pluck('lesson');

        return view('livewire.profile', [
            'user' => $user,
            'totalScore' => $totalScore,
            'totalQuizzes' => $totalQuizzes,
            'completedLessonsCount' => $completedLessonsCount,
            'masteredFlashcardsCount' => $masteredFlashcardsCount,
            'currentLevel' => $currentLevel,
            'currentXp' => $currentXp,
            'nextThreshold' => $nextThreshold,
            'levelProgressPercentage' => $levelProgressPercentage,
            'allAchievements' => $allAchievements,
            'unlockedAchievementIds' => $unlockedAchievementIds,
            'bookmarkedLessons' => $bookmarkedLessons,
        ])->layout('components.layouts.app', ['title' => 'My Profile • Diocese of Livingstone']);
    }
}
