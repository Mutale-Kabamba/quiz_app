<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\DailyChallenge;
use App\Models\Lesson;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserChallengeParticipation;
use App\Services\AdaptiveMasteryService;
use App\Services\GamificationService;
use App\Services\LearningIntelligenceService;
use App\Services\LearningProgressService;
use App\Services\MicroLearningService;
use App\Services\ParishCommunityChallengeService;
use App\Services\RallyPreparationService;
use App\Services\SpacedReviewService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MobileDashboard extends Component
{
    public string $rallyPin = '';
    public bool $showRallyModal = false;
    public bool $showSaintModal = false;
    public bool $showExplainModal = false;
    public string $explainConcept = '';

    public function joinLiveRally()
    {
        $this->validate([
            'rallyPin' => 'required|numeric|digits:6',
        ]);

        return redirect()->to('/quiz?tab=compete');
    }

    public function openExplainModal(string $concept)
    {
        $this->explainConcept = $concept;
        $this->showExplainModal = true;
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        // 1. Learning Progress & Recommendations
        $progressService = app(LearningProgressService::class);
        $intelligenceService = app(LearningIntelligenceService::class);
        $gamificationService = app(GamificationService::class);
        $adaptiveMasteryService = app(AdaptiveMasteryService::class);
        $microLearningService = app(MicroLearningService::class);
        $rallyService = app(RallyPreparationService::class);
        $parishChallengeService = app(ParishCommunityChallengeService::class);
        $spacedReviewService = app(SpacedReviewService::class);

        $continueLesson = $progressService->getContinueLearningLesson($user);
        $categoryProgress = $progressService->getCategoryProgress($user);
        $smartRecommendations = $intelligenceService->getRecommendations($user);

        // 2. Micro Learning ("Learn in 5 Minutes")
        $microLesson = $microLearningService->getTodayMicroLesson($user);
        $microLessonCompleted = $microLesson ? $microLearningService->hasUserCompleted($user, $microLesson) : false;

        // 3. Spaced Reviews Due
        $spacedReviewsCount = $spacedReviewService->getDueReviewsCount($user);

        // 4. Rally Preparation
        $rallyPrep = $rallyService->getActiveRally();
        $rallyReadiness = $rallyService->calculateReadiness($user, $rallyPrep);

        // 5. Parish Community Challenge
        $parishChallenges = $user->parish ? $parishChallengeService->getActiveChallengesForParish($user->parish) : collect();
        $activeParishChallenge = $parishChallenges->first();
        $challengeStandings = $activeParishChallenge ? $parishChallengeService->getChallengeStandings($activeParishChallenge) : null;

        // 6. Weak Areas Analysis
        $weakAreas = $adaptiveMasteryService->getWeakTopics($user, 2);

        // 7. Daily Challenge
        $todayChallenge = DailyChallenge::where('challenge_date', now()->toDateString())
            ->where('is_active', true)
            ->first();

        $challengeCompleted = false;
        if ($todayChallenge) {
            $challengeCompleted = UserChallengeParticipation::where('user_id', $user->id)
                ->where('daily_challenge_id', $todayChallenge->id)
                ->exists();
        }

        // 8. Next Milestone Achievement
        $unlockedAchievementIds = $user->achievements()->pluck('achievement_id');
        $nextAchievement = Achievement::whereNotIn('id', $unlockedAchievementIds)->first();

        // 9. XP and Level Progression
        $currentLevel = $user->level ?? 1;
        $currentXp = $user->xp ?? 0;
        $currentBaseline = $gamificationService->getCurrentLevelBaseline($currentLevel);
        $nextThreshold = $gamificationService->getNextLevelThreshold($currentLevel);
        $levelXpSpan = max(1, $nextThreshold - $currentBaseline);
        $levelProgressPercentage = min(100, (int) round((($currentXp - $currentBaseline) / $levelXpSpan) * 100));

        // 10. Chairperson Stats
        $chairpersonStats = null;
        if ($user->isChairperson() || $user->isSuperAdmin()) {
            $chairpersonStats = [
                'pending_approvals' => User::where('role', 'youth')
                    ->where('status', 'pending')
                    ->when($user->isChairperson(), fn($q) => $q->where('parish_id', $user->parish_id))
                    ->count(),
                'total_parish_youth' => User::where('role', 'youth')
                    ->when($user->isChairperson(), fn($q) => $q->where('parish_id', $user->parish_id))
                    ->count(),
                'active_this_week' => QuizAttempt::whereHas('user', function ($q) use ($user) {
                        if ($user->isChairperson()) {
                            $q->where('parish_id', $user->parish_id);
                        }
                    })
                    ->where('completed_at', '>=', now()->subDays(7))
                    ->count(),
            ];
        }

        return view('livewire.mobile-dashboard', [
            'user' => $user,
            'continueLesson' => $continueLesson,
            'categoryProgress' => $categoryProgress,
            'todayChallenge' => $todayChallenge,
            'challengeCompleted' => $challengeCompleted,
            'nextAchievement' => $nextAchievement,
            'currentXp' => $currentXp,
            'currentLevel' => $currentLevel,
            'nextThreshold' => $nextThreshold,
            'levelProgressPercentage' => $levelProgressPercentage,
            'smartRecommendations' => $smartRecommendations,
            'microLesson' => $microLesson,
            'microLessonCompleted' => $microLessonCompleted,
            'spacedReviewsCount' => $spacedReviewsCount,
            'rallyPrep' => $rallyPrep,
            'rallyReadiness' => $rallyReadiness,
            'activeParishChallenge' => $activeParishChallenge,
            'challengeStandings' => $challengeStandings,
            'weakAreas' => $weakAreas,
            'chairpersonStats' => $chairpersonStats,
        ])->layout('components.layouts.app', ['title' => 'Catholic Formation • Livingstone Diocese']);
    }
}
