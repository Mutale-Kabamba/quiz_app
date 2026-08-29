<?php

namespace App\Services;

use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OfflineSyncService
{
    /**
     * Pushes a completed quiz attempt to the central cloud API
     */
    public function syncAttemptToServer(QuizAttempt $attempt): bool
    {
        try {
            $apiUrl = config('services.central_api.url', 'https://api.livingstonediocese.org');
            $response = Http::timeout(5)
                ->withToken(session('auth_token'))
                ->post($apiUrl . '/api/v1/sync/quiz-attempt', [
                    'id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                    'category_id' => $attempt->category_id,
                    'level' => $attempt->level,
                    'mode' => $attempt->mode,
                    'score' => $attempt->score,
                    'total_questions' => $attempt->total_questions,
                    'correct_answers_count' => $attempt->correct_answers_count,
                    'time_taken_seconds' => $attempt->time_taken_seconds,
                    'completed_at' => $attempt->completed_at->toIso8601String(),
                ]);

            if ($response->successful()) {
                $attempt->update(['is_synced' => true]);
                return true;
            }
        } catch (\Throwable $e) {
            Log::warning("Offline queue: sync pending for attempt {$attempt->id}", [
                'error' => $e->getMessage()
            ]);
        }

        return false;
    }
}
