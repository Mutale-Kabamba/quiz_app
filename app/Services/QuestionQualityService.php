<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\QuizAttemptAnswer;
use Illuminate\Support\Facades\DB;

class QuestionQualityService
{
    /**
     * Analyze performance metrics and flag quality issues for a question
     */
    public function getQuestionQuality(Question $question): array
    {
        $answers = QuizAttemptAnswer::where('question_id', $question->id)->get();
        $totalAnswers = $answers->count();
        $correctCount = $answers->where('is_correct', true)->count();
        $incorrectCount = $totalAnswers - $correctCount;
        $accuracyRate = $totalAnswers > 0 ? (int) round(($correctCount / $totalAnswers) * 100) : 0;
        $reportCount = QuestionReport::where('question_id', $question->id)->count();

        $flags = [];
        $status = 'good';

        if ($totalAnswers >= 5) {
            if ($accuracyRate < 25) {
                $flags[] = '⚠️ Question may be too difficult or ambiguous (Accuracy < 25%)';
                $status = 'warning';
            } elseif ($accuracyRate > 95) {
                $flags[] = 'ℹ️ Question may be too easy (Accuracy > 95%)';
                $status = 'info';
            }
        }

        if ($reportCount > 0) {
            $flags[] = "🚩 {$reportCount} user dispute reports filed by Parish Administrators / Youths";
            $status = 'danger';
        }

        if (empty($flags)) {
            $flags[] = $totalAnswers > 0 ? '✅ Healthy statistical distribution' : '⏳ Awaiting quiz session responses';
        }

        return [
            'total_answers' => $totalAnswers,
            'correct_count' => $correctCount,
            'incorrect_count' => $incorrectCount,
            'accuracy_rate' => $accuracyRate,
            'report_count' => $reportCount,
            'flags' => $flags,
            'status' => $status,
        ];
    }

    /**
     * Get all questions requiring review due to reports or extreme statistical skew
     */
    public function getQuestionsRequiringReview(): array
    {
        $reportedQuestionIds = QuestionReport::where('status', 'pending')->pluck('question_id')->unique();
        return Question::whereIn('id', $reportedQuestionIds)
            ->with(['category', 'reports'])
            ->get()
            ->all();
    }
}
