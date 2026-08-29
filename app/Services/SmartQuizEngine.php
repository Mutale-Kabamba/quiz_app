<?php

namespace App\Services;

use App\Models\CompetitionLockedQuestionSet;
use App\Models\QuestionBankItem;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizBlueprint;
use App\Models\User;
use Illuminate\Support\Collection;

class SmartQuizEngine
{
    /**
     * Generate an adaptive or blueprint-based quiz selection for a user
     */
    public function generateQuiz(
        QuizBlueprint $blueprint,
        ?User $user = null,
        ?int $topicId = null
    ): Collection {
        $count = $blueprint->question_count ?: 10;
        $unseenRatio = ($blueprint->unseen_question_ratio ?? 70) / 100.0;
        $unseenTarget = (int) round($count * $unseenRatio);
        $revisionTarget = $count - $unseenTarget;

        $baseQuery = QuestionBankItem::where('status', 'PUBLISHED')
            ->where('is_practice_eligible', true);

        if ($topicId) {
            $baseQuery->where('topic_id', $topicId);
        } elseif ($blueprint->level_id) {
            $baseQuery->where('level_id', $blueprint->level_id);
        }

        // Questions previously answered by user
        $answeredQuestionIds = [];
        if ($user) {
            $answeredQuestionIds = QuizAttemptAnswer::whereHas('quizAttempt', fn($q) => $q->where('user_id', $user->id))
                ->pluck('question_id')
                ->unique()
                ->toArray();
        }

        // 1. Unseen Questions
        $unseenQuestions = (clone $baseQuery)
            ->whereNotIn('id', $answeredQuestionIds)
            ->with('options')
            ->inRandomOrder()
            ->limit($unseenTarget)
            ->get();

        // 2. Revision Questions (or fallback to any if not enough revision available)
        $needed = $count - $unseenQuestions->count();
        $revisionQuery = (clone $baseQuery)->with('options');

        if (!empty($answeredQuestionIds) && $needed > 0) {
            $revisionQuery->whereIn('id', $answeredQuestionIds);
        }

        $revisionQuestions = $revisionQuery->inRandomOrder()->limit($needed)->get();

        $merged = $unseenQuestions->merge($revisionQuestions)->unique('id');

        // Fallback if still under target count
        if ($merged->count() < $count) {
            $remainingNeeded = $count - $merged->count();
            $fillers = (clone $baseQuery)
                ->whereNotIn('id', $merged->pluck('id')->toArray())
                ->with('options')
                ->inRandomOrder()
                ->limit($remainingNeeded)
                ->get();
            $merged = $merged->merge($fillers);
        }

        return $merged->take($count);
    }

    /**
     * Lock an official competition question set for tournament integrity
     */
    public function lockCompetitionRound(
        string $competitionIdentifier,
        string $roundName,
        int $roundNumber,
        Collection $questions,
        ?User $admin = null
    ): CompetitionLockedQuestionSet {
        $snapshots = $questions->map(function (QuestionBankItem $q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'question_type' => $q->question_type,
                'explanation' => $q->explanation,
                'reference_citation' => $q->reference_citation,
                'editorial_difficulty' => $q->editorial_difficulty,
                'options' => $q->options->map(fn($opt) => [
                    'key' => $opt->option_key,
                    'text' => $opt->option_text,
                    'is_correct' => $opt->is_correct,
                    'explanation' => $opt->explanation_why_incorrect,
                ])->toArray(),
            ];
        })->toArray();

        return CompetitionLockedQuestionSet::create([
            'competition_identifier' => $competitionIdentifier,
            'round_name' => $roundName,
            'round_number' => $roundNumber,
            'locked_question_snapshots' => $snapshots,
            'question_count' => count($snapshots),
            'is_locked' => true,
            'locked_by' => $admin?->id,
            'locked_at' => now(),
        ]);
    }
}
