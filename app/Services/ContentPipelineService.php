<?php

namespace App\Services;

use App\Models\ContentReviewLog;
use App\Models\QuestionBankItem;
use App\Models\StudyResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContentPipelineService
{
    /**
     * Submit an item for theological & editorial review
     */
    public function submitForReview(Model $item, User $submitter, string $comments = ''): void
    {
        $oldStatus = $item->status;
        $item->update(['status' => 'UNDER_REVIEW']);

        ContentReviewLog::create([
            'reviewable_type' => get_class($item),
            'reviewable_id' => (string) $item->id,
            'reviewer_id' => $submitter->id,
            'action' => 'SUBMITTED_FOR_REVIEW',
            'reviewer_comments' => $comments,
            'old_status' => $oldStatus,
            'new_status' => 'UNDER_REVIEW',
        ]);
    }

    /**
     * Approve and optionally publish content by an authorized theological reviewer
     */
    public function approveContent(
        Model $item,
        User $reviewer,
        bool $publishImmediately = true,
        int $theologicalRating = 5,
        int $clarityRating = 5,
        string $comments = ''
    ): void {
        DB::transaction(function () use ($item, $reviewer, $publishImmediately, $theologicalRating, $clarityRating, $comments) {
            $oldStatus = $item->status;
            $newStatus = $publishImmediately ? 'PUBLISHED' : 'APPROVED';

            $item->update([
                'status' => $newStatus,
                'theological_reviewer_id' => $reviewer->id,
                'reviewed_at' => now(),
                'published_at' => $publishImmediately ? now() : $item->published_at,
            ]);

            ContentReviewLog::create([
                'reviewable_type' => get_class($item),
                'reviewable_id' => (string) $item->id,
                'reviewer_id' => $reviewer->id,
                'action' => $publishImmediately ? 'PUBLISHED' : 'APPROVED',
                'theological_accuracy_rating' => $theologicalRating,
                'clarity_rating' => $clarityRating,
                'reviewer_comments' => $comments,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);
        });
    }

    /**
     * Reject or request revisions on content with reviewer feedback
     */
    public function requestRevision(Model $item, User $reviewer, string $feedback): void
    {
        $oldStatus = $item->status;
        $item->update(['status' => 'NEEDS_REVISION']);

        ContentReviewLog::create([
            'reviewable_type' => get_class($item),
            'reviewable_id' => (string) $item->id,
            'reviewer_id' => $reviewer->id,
            'action' => 'REQUESTED_REVISION',
            'reviewer_comments' => $feedback,
            'old_status' => $oldStatus,
            'new_status' => 'NEEDS_REVISION',
        ]);
    }
}
