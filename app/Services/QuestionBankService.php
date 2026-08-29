<?php

namespace App\Services;

use App\Models\QuestionBankItem;
use App\Models\QuestionOption;
use App\Models\QuestionVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionBankService
{
    /**
     * Create a new Question with normalized options, hash, and version 1 snapshot
     */
    public function createQuestion(array $data, array $options = [], ?User $creator = null): QuestionBankItem
    {
        return DB::transaction(function () use ($data, $options, $creator) {
            $questionText = $data['question_text'];
            $hash = app(DuplicateDetectionService::class)->generateSimilarityHash($questionText);

            $question = QuestionBankItem::create([
                'id' => (string) Str::uuid(),
                'track_id' => $data['track_id'],
                'category_id' => $data['category_id'] ?? null,
                'topic_id' => $data['topic_id'] ?? null,
                'subtopic_id' => $data['subtopic_id'] ?? null,
                'concept_id' => $data['concept_id'] ?? null,
                'level_id' => $data['level_id'] ?? null,
                'age_band_id' => $data['age_band_id'] ?? null,
                'bloom_id' => $data['bloom_id'] ?? null,
                'question_type' => $data['question_type'] ?? 'MULTIPLE_CHOICE',
                'question_text' => $questionText,
                'explanation' => $data['explanation'] ?? null,
                'teaching_point' => $data['teaching_point'] ?? null,
                'correct_answer_payload' => $data['correct_answer_payload'] ?? null,
                'reference_citation' => $data['reference_citation'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'language_code' => $data['language_code'] ?? 'en',
                'editorial_difficulty' => $data['editorial_difficulty'] ?? 'MEDIUM',
                'status' => $data['status'] ?? 'DRAFT',
                'is_competition_eligible' => $data['is_competition_eligible'] ?? true,
                'is_practice_eligible' => $data['is_practice_eligible'] ?? true,
                'duplicate_similarity_hash' => $hash,
                'author_id' => $creator?->id,
                'current_version' => 1,
            ]);

            // Save normalized options
            $optionsSnapshot = [];
            $correctKey = '';
            foreach ($options as $idx => $opt) {
                $key = $opt['option_key'] ?? chr(65 + $idx); // 'A', 'B', 'C', 'D'
                $isCorrect = (bool) ($opt['is_correct'] ?? false);
                if ($isCorrect) {
                    $correctKey = $key;
                }

                QuestionOption::create([
                    'question_bank_item_id' => $question->id,
                    'option_key' => $key,
                    'option_text' => $opt['option_text'],
                    'is_correct' => $isCorrect,
                    'explanation_why_incorrect' => $opt['explanation_why_incorrect'] ?? null,
                    'sort_order' => $opt['sort_order'] ?? ($idx + 1),
                ]);

                $optionsSnapshot[] = [
                    'key' => $key,
                    'text' => $opt['option_text'],
                    'is_correct' => $isCorrect,
                    'explanation' => $opt['explanation_why_incorrect'] ?? null,
                ];
            }

            // Create initial Version 1 snapshot
            QuestionVersion::create([
                'question_bank_item_id' => $question->id,
                'version_number' => 1,
                'question_text' => $questionText,
                'options_snapshot' => $optionsSnapshot,
                'correct_answer_snapshot' => $correctKey ?: ($data['correct_answer_payload'] ?? 'A'),
                'explanation_snapshot' => $data['explanation'] ?? null,
                'reference_citation_snapshot' => $data['reference_citation'] ?? null,
                'changelog_notes' => 'Initial creation',
                'created_by' => $creator?->id,
            ]);

            return $question;
        });
    }

    /**
     * Record a user attempt on a question and update item analytics & empirical difficulty
     */
    public function recordAttemptStats(QuestionBankItem $question, bool $isCorrect, float $responseTimeSeconds): void
    {
        $newServed = $question->times_served + 1;
        $newAnswered = $question->times_answered + 1;
        $newCorrect = $question->times_correct + ($isCorrect ? 1 : 0);
        $newIncorrect = $question->times_incorrect + ($isCorrect ? 0 : 1);

        // Cumulative rolling average for response time
        $oldAvgTime = $question->avg_response_time_seconds;
        $newAvgTime = round((($oldAvgTime * $question->times_answered) + $responseTimeSeconds) / $newAnswered, 2);

        // Empirical difficulty: % of answers that are correct (1.0 = Easiest, 0.0 = Hardest)
        $empiricalDifficulty = round($newCorrect / $newAnswered, 3);

        // Dynamic Question Health Score (0-100)
        // Questions with very high response times, 0% or 100% extremes with high attempt volume get balanced scores
        $healthScore = 100;
        if ($newAnswered >= 20) {
            if ($empiricalDifficulty < 0.10) { // Too hard / broken question
                $healthScore -= 30;
            } elseif ($empiricalDifficulty > 0.98) { // Trivial question
                $healthScore -= 10;
            }
        }

        $question->update([
            'times_served' => $newServed,
            'times_answered' => $newAnswered,
            'times_correct' => $newCorrect,
            'times_incorrect' => $newIncorrect,
            'avg_response_time_seconds' => $newAvgTime,
            'empirical_difficulty' => $empiricalDifficulty,
            'health_score' => max(0, min(100, $healthScore)),
        ]);
    }
}
