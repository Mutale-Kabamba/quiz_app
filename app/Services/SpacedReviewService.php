<?php

namespace App\Services;

use App\Models\QuestionBankItem;
use App\Models\TaxonomyConcept;
use App\Models\User;
use App\Models\UserSpacedQuestionReview;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class SpacedReviewService
{
    /**
     * Standard intervals in days based on consecutive correct responses.
     */
    protected array $intervals = [1, 3, 7, 14, 30];

    /**
     * Record a mistake on a question or concept to schedule future reinforcement.
     */
    public function recordMistake(User $user, string $questionId, ?int $conceptId = null, ?int $topicId = null): UserSpacedQuestionReview
    {
        $review = UserSpacedQuestionReview::firstOrNew([
            'user_id' => $user->id,
            'question_id' => $questionId,
        ]);

        $review->concept_id = $conceptId ?? $review->concept_id;
        $review->topic_id = $topicId ?? $review->topic_id;
        $review->mistake_count = ($review->mistake_count ?? 0) + 1;
        $review->consecutive_correct = 0;
        $review->interval_days = 1; // Reset to 1-day interval
        $review->last_reviewed_at = now();
        $review->next_review_date = Carbon::today()->addDay();
        $review->is_mastered = false;
        $review->save();

        return $review;
    }

    /**
     * Record a successful answer during a review or quiz attempt.
     */
    public function recordSuccess(User $user, string $questionId): ?UserSpacedQuestionReview
    {
        $review = UserSpacedQuestionReview::where('user_id', $user->id)
            ->where('question_id', $questionId)
            ->first();

        if (!$review) {
            return null;
        }

        $review->consecutive_correct += 1;
        $intervalIndex = min($review->consecutive_correct, count($this->intervals) - 1);
        $review->interval_days = $this->intervals[$intervalIndex];
        $review->last_reviewed_at = now();
        $review->next_review_date = Carbon::today()->addDays($review->interval_days);

        if ($review->consecutive_correct >= count($this->intervals)) {
            $review->is_mastered = true;
        }

        $review->save();

        return $review;
    }

    /**
     * Get questions scheduled for review today for the given user.
     */
    public function getDueReviews(User $user, int $limit = 10): Collection
    {
        return UserSpacedQuestionReview::with(['concept', 'topic', 'question'])
            ->where('user_id', $user->id)
            ->where('is_mastered', false)
            ->where('next_review_date', '<=', Carbon::today())
            ->orderBy('next_review_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Get count of reviews due today.
     */
    public function getDueReviewsCount(User $user): int
    {
        return UserSpacedQuestionReview::where('user_id', $user->id)
            ->where('is_mastered', false)
            ->where('next_review_date', '<=', Carbon::today())
            ->count();
    }
}
