<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionBankItem;
use App\Models\RallyPreparation;
use App\Models\User;
use App\Models\UserRallyReadiness;
use App\Models\UserTopicMastery;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class RallyPreparationService
{
    public function __construct(
        protected GamificationService $gamificationService,
        protected StreakService $streakService
    ) {}

    /**
     * Get or create the active Diocesan Youth Rally preparation program.
     */
    public function getActiveRally(): RallyPreparation
    {
        $rally = RallyPreparation::where('is_active', true)->first();

        if (!$rally) {
            $rally = RallyPreparation::create([
                'title' => '2026 Livingstone Diocesan Youth Rally Preparation',
                'slug' => '2026-livingstone-diocesan-youth-rally',
                'rally_date' => Carbon::today()->addDays(18),
                'description' => 'Comprehensive multi-domain preparation for the annual Livingstone Diocesan Youth Catechetical Rally.',
                'target_questions_count' => 200,
                'domain_weights' => [
                    'scripture' => 25,
                    'catechism' => 30,
                    'history' => 15,
                    'saints' => 15,
                    'doctrine' => 15,
                ],
                'is_active' => true,
            ]);
        }

        return $rally;
    }

    /**
     * Calculate readiness breakdown for a youth.
     */
    public function calculateReadiness(User $user, RallyPreparation $rally): UserRallyReadiness
    {
        $readiness = UserRallyReadiness::firstOrNew([
            'user_id' => $user->id,
            'rally_id' => $rally->id,
        ]);

        // Compute domain mastery from UserTopicMastery
        $masteries = UserTopicMastery::where('user_id', $user->id)->get();
        $avgMastery = $masteries->isNotEmpty() ? round($masteries->avg('mastery_score')) : 50;

        $scripture = min(100, (int) round($avgMastery * 1.05));
        $catechism = min(100, (int) round($avgMastery * 0.95));
        $history = min(100, (int) round($avgMastery * 0.85));
        $saints = min(100, (int) round($avgMastery * 1.1));
        $doctrine = min(100, (int) round($avgMastery * 0.9));

        $overall = (int) round(($scripture * 0.25) + ($catechism * 0.30) + ($history * 0.15) + ($saints * 0.15) + ($doctrine * 0.15));

        $readiness->scripture_readiness = max(20, $scripture);
        $readiness->catechism_readiness = max(20, $catechism);
        $readiness->history_readiness = max(15, $history);
        $readiness->saints_readiness = max(20, $saints);
        $readiness->doctrine_readiness = max(20, $doctrine);
        $readiness->overall_readiness_percentage = max(25, $overall);
        $readiness->save();

        return $readiness;
    }

    /**
     * Record a completed rally training drill.
     */
    public function recordTrainingDrill(User $user, RallyPreparation $rally, int $questionsAnswered = 10, int $correctCount = 8): UserRallyReadiness
    {
        $readiness = $this->calculateReadiness($user, $rally);
        $readiness->training_questions_answered += $questionsAnswered;
        $readiness->last_trained_at = now();

        // Increment readiness based on practice
        $readiness->overall_readiness_percentage = min(100, $readiness->overall_readiness_percentage + 2);
        $readiness->save();

        // Award training XP & advance streak
        $this->gamificationService->awardXp(
            $user,
            $correctCount * 10,
            "Rally Prep Drill ({$correctCount}/{$questionsAnswered} correct)",
            'rally_training',
            (string) $rally->id
        );

        $this->streakService->recordFormationActivity($user);

        return $readiness;
    }
}
