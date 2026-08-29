<?php

namespace App\Services;

use App\Models\Flashcard;
use App\Models\FlashcardReview;
use App\Models\User;
use Carbon\Carbon;

class FlashcardService
{
    /**
     * Get flashcards due for review today for a user
     */
    public function getDueFlashcards(User $user, ?int $categoryId = null, int $limit = 10): \Illuminate\Support\Collection
    {
        $today = Carbon::today();

        // 1. Cards already in user review queue due today or earlier
        $dueCardIds = FlashcardReview::where('user_id', $user->id)
            ->where('next_review_at', '<=', $today)
            ->pluck('flashcard_id');

        // 2. Fetch cards due + unreviewed cards up to limit
        $query = Flashcard::where('status', 'published');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Prioritize due cards, then unreviewed cards
        $cards = $query->where(function ($q) use ($dueCardIds, $user) {
            $q->whereIn('id', $dueCardIds)
              ->orWhereDoesntHave('reviews', fn($sub) => $sub->where('user_id', $user->id));
        })->inRandomOrder()->limit($limit)->get();

        return $cards;
    }

    /**
     * Record review feedback (1=Again, 2=Good, 3=Easy) and compute next review interval
     */
    public function recordReview(User $user, string $flashcardId, int $rating): FlashcardReview
    {
        $today = Carbon::today();

        // Spaced repetition interval rules
        $daysToAdd = match ($rating) {
            1 => 1, // Again: review tomorrow
            2 => 3, // Good: review in 3 days
            3 => 7, // Easy: review in 7 days
            default => 2,
        };

        $nextReview = $today->copy()->addDays($daysToAdd);

        $review = FlashcardReview::where('user_id', $user->id)
            ->where('flashcard_id', $flashcardId)
            ->first();

        if ($review) {
            $review->update([
                'rating' => $rating,
                'reviewed_at' => now(),
                'next_review_at' => $nextReview,
                'review_count' => $review->review_count + 1,
            ]);
        } else {
            $review = FlashcardReview::create([
                'user_id' => $user->id,
                'flashcard_id' => $flashcardId,
                'rating' => $rating,
                'reviewed_at' => now(),
                'next_review_at' => $nextReview,
                'review_count' => 1,
            ]);
        }

        return $review;
    }

    /**
     * Get review statistics for user
     */
    public function getUserStats(User $user): array
    {
        $totalReviewed = FlashcardReview::where('user_id', $user->id)->count();
        $mastered = FlashcardReview::where('user_id', $user->id)->where('rating', 3)->count();
        $dueToday = FlashcardReview::where('user_id', $user->id)
            ->where('next_review_at', '<=', Carbon::today())
            ->count();

        return [
            'total_reviewed' => $totalReviewed,
            'mastered' => $mastered,
            'due_today' => $dueToday,
        ];
    }
}
