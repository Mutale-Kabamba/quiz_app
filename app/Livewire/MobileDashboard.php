<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DailyChallenge;
use App\Models\Deanery;
use App\Models\Lesson;
use App\Models\Parish;
use App\Models\ParishAnnouncement;
use App\Models\ParishEvent;
use App\Models\ParishFormationChallenge;
use App\Models\ParishTransfer;
use App\Models\QuizAttempt;
use App\Models\SaintProfile;
use App\Models\User;
use App\Models\UserChallengeParticipation;
use App\Services\AdaptiveMasteryService;
use App\Services\DiocesanAnalyticsService;
use App\Services\GamificationService;
use App\Services\LearningIntelligenceService;
use App\Services\LearningProgressService;
use App\Services\LiturgicalCalendarService;
use App\Services\MicroLearningService;
use App\Services\ParishCommunityChallengeService;
use App\Services\ParishDashboardService;
use App\Services\RallyPreparationService;
use App\Services\SpacedReviewService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MobileDashboard extends Component
{
    public string $rallyPin = '';
    public bool $showRallyModal = false;
    public bool $showSaintModal = false;
    public ?SaintProfile $selectedSaint = null;
    public bool $showExplainModal = false;
    public string $explainConcept = '';

    public function joinLiveRally()
    {
        $this->validate([
            'rallyPin' => 'required|numeric|digits:6',
        ]);

        return redirect()->to('/quiz?tab=compete');
    }

    public function openSaintModal(int $saintId)
    {
        $this->selectedSaint = SaintProfile::find($saintId);
        $this->showSaintModal = true;
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

        $liturgicalService = app(LiturgicalCalendarService::class);
        $liturgicalContext = $liturgicalService->getCurrentContext();

        // =========================================================================
        // A. SUPER ADMIN EXECUTIVE OVERVIEW DATA
        // =========================================================================
        if ($user->isSuperAdmin()) {
            $analyticsService = app(DiocesanAnalyticsService::class);
            $diocesanKpis = $analyticsService->getDiocesanKpis();
            $deaneries = Deanery::withCount('parishes')->get();
            $pendingTransfersCount = ParishTransfer::where('status', 'pending')->count();
            $recentAudits = AuditLog::with('user')->latest()->take(5)->get();

            return view('livewire.mobile-dashboard', [
                'user' => $user,
                'liturgicalContext' => $liturgicalContext,
                'diocesanKpis' => $diocesanKpis,
                'deaneries' => $deaneries,
                'pendingTransfersCount' => $pendingTransfersCount,
                'recentAudits' => $recentAudits,
            ])->layout('components.layouts.app', ['title' => 'Diocesan Command • Livingstone Diocese']);
        }

        // =========================================================================
        // B. PARISH ADMIN (CHAIRPERSON) EXECUTIVE OVERVIEW DATA
        // =========================================================================
        if ($user->isChairperson()) {
            $parish = $user->parish ?? Parish::first();
            $parishService = app(ParishDashboardService::class);
            $parishKpis = $parishService->getParishKpis($parish->id);
            $formationHealth = $parishService->getFormationHealth($parish->id);
            
            $pendingApprovals = User::where('role', 'youth')
                ->where('status', 'pending')
                ->where('parish_id', $parish->id)
                ->take(5)
                ->get();

            $recentAnnouncements = ParishAnnouncement::where('parish_id', $parish->id)->latest()->take(3)->get();
            $upcomingEvents = ParishEvent::where('parish_id', $parish->id)->latest()->take(3)->get();
            $activeChallenges = ParishFormationChallenge::where('parish_id', $parish->id)->where('is_active', true)->get();

            return view('livewire.mobile-dashboard', [
                'user' => $user,
                'liturgicalContext' => $liturgicalContext,
                'parish' => $parish,
                'parishKpis' => $parishKpis,
                'formationHealth' => $formationHealth,
                'pendingApprovals' => $pendingApprovals,
                'recentAnnouncements' => $recentAnnouncements,
                'upcomingEvents' => $upcomingEvents,
                'activeChallenges' => $activeChallenges,
            ])->layout('components.layouts.app', ['title' => "Parish Leadership • {$parish->name}"]);
        }

        // =========================================================================
        // C. YOUTH LEARNER FORMATION DATA
        // =========================================================================
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

        $microLesson = $microLearningService->getTodayMicroLesson($user);
        $microLessonCompleted = $microLesson ? $microLearningService->hasUserCompleted($user, $microLesson) : false;
        $spacedReviewsCount = $spacedReviewService->getDueReviewsCount($user);

        $rallyPrep = $rallyService->getActiveRally();
        $rallyReadiness = $rallyService->calculateReadiness($user, $rallyPrep);

        $parishChallenges = $user->parish ? $parishChallengeService->getActiveChallengesForParish($user->parish) : collect();
        $activeParishChallenge = $parishChallenges->first();
        $challengeStandings = $activeParishChallenge ? $parishChallengeService->getChallengeStandings($activeParishChallenge) : null;

        $weakAreas = $adaptiveMasteryService->getWeakTopics($user, 2);

        $todayChallenge = DailyChallenge::where('challenge_date', now()->toDateString())
            ->where('is_active', true)
            ->first();

        $challengeCompleted = false;
        if ($todayChallenge) {
            $challengeCompleted = UserChallengeParticipation::where('user_id', $user->id)
                ->where('daily_challenge_id', $todayChallenge->id)
                ->exists();
        }

        $unlockedAchievementIds = $user->achievements()->pluck('achievement_id');
        $nextAchievement = Achievement::whereNotIn('id', $unlockedAchievementIds)->first();

        $featuredSaints = SaintProfile::take(4)->get();
        $featuredCategories = Category::withCount(['lessons', 'questions'])->orderBy('display_order')->take(4)->get();

        $currentLevel = $user->level ?? 1;
        $currentXp = $user->xp ?? 0;
        $currentBaseline = $gamificationService->getCurrentLevelBaseline($currentLevel);
        $nextThreshold = $gamificationService->getNextLevelThreshold($currentLevel);
        $levelXpSpan = max(1, $nextThreshold - $currentBaseline);
        $levelProgressPercentage = min(100, (int) round((($currentXp - $currentBaseline) / $levelXpSpan) * 100));

        // Formation title mapping
        $levelTitle = match($currentLevel) {
            1 => 'Seeker of Truth',
            2 => 'Faithful Disciple',
            3 => 'Catechetical Scholar',
            4 => 'Scripture Pillar',
            5 => 'Diocesan Evangelist',
            default => 'Youth Champion',
        };

        return view('livewire.mobile-dashboard', [
            'user' => $user,
            'liturgicalContext' => $liturgicalContext,
            'continueLesson' => $continueLesson,
            'categoryProgress' => $categoryProgress,
            'todayChallenge' => $todayChallenge,
            'challengeCompleted' => $challengeCompleted,
            'nextAchievement' => $nextAchievement,
            'currentXp' => $currentXp,
            'currentLevel' => $currentLevel,
            'levelTitle' => $levelTitle,
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
            'featuredSaints' => $featuredSaints,
            'featuredCategories' => $featuredCategories,
        ])->layout('components.layouts.app', ['title' => 'Catholic Formation • Livingstone Diocese']);
    }
}
