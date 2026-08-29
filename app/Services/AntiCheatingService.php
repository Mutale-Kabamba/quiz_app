<?php

namespace App\Services;

use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;

class AntiCheatingService
{
    /**
     * Scan recent quiz attempts for suspicious patterns
     */
    public function getSuspiciousActivity(): array
    {
        $flags = [];
        $recentAttempts = QuizAttempt::with('user')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get();

        foreach ($recentAttempts as $attempt) {
            // 1. Check Impossible Speed (< 1 second per question with 100% score)
            $totalQuestions = $attempt->total_questions > 0 ? $attempt->total_questions : 10;
            $timeTaken = $attempt->time_taken_seconds ?? 0;
            $avgTimePerQuestion = $timeTaken / $totalQuestions;

            if ($timeTaken > 0 && $avgTimePerQuestion < 1.2 && $attempt->score >= 80) {
                $flags[] = [
                    'user_id' => $attempt->user_id,
                    'user_name' => $attempt->user?->name ?? 'Unknown User',
                    'parish' => $attempt->user?->parish?->name ?? '—',
                    'type' => 'impossible_speed',
                    'severity' => 'warning',
                    'detail' => "Completed {$totalQuestions} questions in {$timeTaken}s ({$avgTimePerQuestion}s/question) with score {$attempt->score}.",
                    'attempt_id' => $attempt->id,
                    'date' => $attempt->created_at->format('M d, H:i'),
                ];
            }
        }

        return $flags;
    }
}
