<?php

namespace App\Services;

use App\Models\TaxonomyTopic;
use App\Models\User;
use App\Models\UserTopicMastery;

class AdaptiveMasteryService
{
    /**
     * Record study or quiz activity on a topic and update mastery metrics
     */
    public function recordTopicActivity(
        User $user,
        int $topicId,
        int $attemptedCount,
        int $correctCount,
        array $weakConceptIds = [],
        array $strongConceptIds = []
    ): UserTopicMastery {
        $mastery = UserTopicMastery::firstOrCreate(
            ['user_id' => $user->id, 'topic_id' => $topicId],
            [
                'mastery_score' => 0,
                'confidence_level' => 'LOW',
                'questions_attempted' => 0,
                'questions_correct' => 0,
                'lessons_completed' => 0,
            ]
        );

        $newAttempted = $mastery->questions_attempted + $attemptedCount;
        $newCorrect = $mastery->questions_correct + $correctCount;

        // Accuracy percentage (0-100)
        $accuracy = $newAttempted > 0 ? round(($newCorrect / $newAttempted) * 100) : 0;

        // Confidence index based on attempt volume
        $confidence = match (true) {
            $newAttempted >= 30 => 'HIGH',
            $newAttempted >= 10 => 'MEDIUM',
            default => 'LOW',
        };

        // Weighted Mastery calculation: blends accuracy with completion volume
        $volumeFactor = min(1.0, $newAttempted / 20.0);
        $masteryScore = (int) round($accuracy * $volumeFactor);

        $mergedWeak = array_values(array_unique(array_merge($mastery->weak_concept_ids ?? [], $weakConceptIds)));
        $mergedStrong = array_values(array_unique(array_merge($mastery->strong_concept_ids ?? [], $strongConceptIds)));

        // Remove strong from weak
        $cleanedWeak = array_diff($mergedWeak, $mergedStrong);

        $mastery->update([
            'questions_attempted' => $newAttempted,
            'questions_correct' => $newCorrect,
            'mastery_score' => min(100, max(0, $masteryScore)),
            'confidence_level' => $confidence,
            'weak_concept_ids' => array_values($cleanedWeak),
            'strong_concept_ids' => array_values($mergedStrong),
            'last_assessed_at' => now(),
        ]);

        return $mastery;
    }

    /**
     * Get user weak topics across all tracks
     */
    public function getWeakTopics(User $user, int $limit = 5): \Illuminate\Support\Collection
    {
        return UserTopicMastery::where('user_id', $user->id)
            ->where('mastery_score', '<', 60)
            ->where('questions_attempted', '>=', 3)
            ->with('topic')
            ->orderBy('mastery_score')
            ->limit($limit)
            ->get();
    }
}
