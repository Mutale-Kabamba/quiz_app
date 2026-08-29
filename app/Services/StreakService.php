<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class StreakService
{
    /**
     * Record a qualifying formation action and update user streak
     */
    public function recordFormationActivity(User $user): array
    {
        $today = Carbon::today();
        $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date)->startOfDay() : null;

        $streakUpdated = false;
        $currentStreak = $user->current_streak ?? 0;
        $longestStreak = $user->longest_streak ?? 0;

        if (!$lastActivity) {
            // First time activity
            $currentStreak = 1;
            $streakUpdated = true;
        } elseif ($lastActivity->equalTo($today)) {
            // Already active today, maintain streak
            $streakUpdated = false;
        } elseif ($lastActivity->equalTo($today->copy()->subDay())) {
            // Active yesterday, increment streak
            $currentStreak += 1;
            $streakUpdated = true;
        } else {
            // Missed a day or more, reset streak to 1
            $currentStreak = 1;
            $streakUpdated = true;
        }

        if ($currentStreak > $longestStreak) {
            $longestStreak = $currentStreak;
        }

        $user->update([
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak,
            'last_activity_date' => $today,
        ]);

        return [
            'streak_updated' => $streakUpdated,
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak,
        ];
    }
}
