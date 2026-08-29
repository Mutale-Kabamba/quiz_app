<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\XpTransaction;
use Illuminate\Support\Facades\Log;

class GamificationService
{
    /**
     * Award XP to user via authoritative ledger, recompute level, and evaluate milestone badges
     */
    public function awardXp(
        User $user,
        int $amount,
        string $reason = '',
        string $sourceType = 'activity',
        ?string $sourceId = null
    ): array {
        if ($amount <= 0) {
            return ['xp_gained' => 0, 'leveled_up' => false, 'new_level' => $user->level];
        }

        $oldLevel = $user->level ?? 1;

        // Record in immutable XP Ledger
        app(XpLedgerService::class)->awardXp(
            $user,
            $amount,
            $sourceType,
            $sourceId,
            $reason ?: "Formation XP: {$sourceType}"
        );

        $user->refresh();
        $newXp = $user->xp;
        $computedLevel = $user->level;
        $leveledUp = $computedLevel > $oldLevel;

        $unlockedAchievements = $this->evaluateAchievements($user);

        return [
            'xp_gained' => $amount,
            'total_xp' => $newXp,
            'leveled_up' => $leveledUp,
            'new_level' => $computedLevel,
            'unlocked_achievements' => $unlockedAchievements,
        ];
    }

    /**
     * Get XP required to enter the given level
     */
    public function getCurrentLevelBaseline(int $level): int
    {
        if ($level <= 1) {
            return 0;
        }
        return (int) (($level - 1) ** 2 * 100);
    }

    /**
     * Get XP required to reach the next level
     */
    public function getNextLevelXpRequirement(int $level): int
    {
        return (int) ($level ** 2 * 100);
    }

    /**
     * Alias for getNextLevelXpRequirement
     */
    public function getNextLevelThreshold(int $level): int
    {
        return $this->getNextLevelXpRequirement($level);
    }

    /**
     * Compute completion percentage towards next level (0-100%)
     */
    public function getLevelProgressPercentage(User $user): int
    {
        $level = $user->level ?? 1;
        $xp = $user->xp ?? 0;
        $baseline = $this->getCurrentLevelBaseline($level);
        $next = $this->getNextLevelXpRequirement($level);
        $span = max(1, $next - $baseline);

        return (int) min(100, max(0, round((($xp - $baseline) / $span) * 100)));
    }

    /**
     * Evaluate and award milestone badges based on user performance
     */
    public function evaluateAchievements(User $user): array
    {
        $unlockedNow = [];
        $achievements = Achievement::all();

        $lessonsCount = $user->lessonProgress()->where('is_completed', true)->count();
        $quizCount = $user->quizAttempts()->count();
        $flashcardCount = $user->flashcardReviews()->count();
        $streak = $user->current_streak ?? 0;
        $totalXp = $user->xp ?? 0;

        foreach ($achievements as $ach) {
            // Check if already unlocked
            $alreadyHas = UserAchievement::where('user_id', $user->id)
                ->where('achievement_id', $ach->id)
                ->exists();

            if ($alreadyHas) {
                continue;
            }

            $unlocked = false;
            $type = $ach->criteria_type ?? $ach->type ?? 'xp_threshold';
            $threshold = (int) ($ach->criteria_threshold ?? $ach->threshold ?? 0);

            switch ($type) {
                case 'first_quiz':
                case 'quiz_count':
                    $unlocked = $quizCount >= ($threshold ?: 1);
                    break;
                case 'first_lesson':
                case 'lesson_count':
                    $unlocked = $lessonsCount >= ($threshold ?: 1);
                    break;
                case 'flashcard_count':
                    $unlocked = $flashcardCount >= ($threshold ?: 1);
                    break;
                case 'streak_days':
                    $unlocked = $streak >= $threshold;
                    break;
                case 'xp_milestone':
                case 'xp_threshold':
                    $unlocked = $totalXp >= $threshold;
                    break;
                case 'parish_rank_1':
                    $isFirst = User::where('parish_id', $user->parish_id)
                        ->where('role', 'youth')
                        ->orderByDesc('xp')
                        ->value('id') === $user->id;
                    $unlocked = $isFirst && $totalXp >= 100;
                    break;
            }

            if ($unlocked) {
                UserAchievement::create([
                    'user_id' => $user->id,
                    'achievement_id' => $ach->id,
                    'unlocked_at' => now(),
                ]);

                // Award bonus XP for achievement unlock (without recursive evaluation)
                if (($ach->xp_reward ?? 0) > 0) {
                    app(XpLedgerService::class)->awardXp(
                        $user,
                        (int) $ach->xp_reward,
                        'achievement_unlocked',
                        (string) $ach->id,
                        "Achievement badge: {$ach->title}"
                    );
                }

                $unlockedNow[] = $ach;
            }
        }

        return $unlockedNow;
    }
}
