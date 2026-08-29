<?php

namespace App\Jobs;

use App\Models\ContentGenerationJob;
use App\Models\QuestionBankItem;
use App\Services\DuplicateDetectionService;
use App\Services\QuestionBankService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateContentBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $jobId;

    public function __construct(string $jobId)
    {
        $this->jobId = $jobId;
    }

    public function handle(): void
    {
        $jobRecord = ContentGenerationJob::find($this->jobId);
        if (!$jobRecord) {
            return;
        }

        $jobRecord->update(['status' => 'PROCESSING', 'started_at' => now()]);

        $quantity = $jobRecord->requested_quantity;
        $trackId = $jobRecord->track_id;
        $topicId = $jobRecord->topic_id;
        $levelId = $jobRecord->level_id;

        $generated = 0;
        $accepted = 0;
        $duplicates = 0;
        $failed = 0;

        $questionBankService = app(QuestionBankService::class);
        $duplicateService = app(DuplicateDetectionService::class);

        // Simulation/batch synthesis loop
        for ($i = 1; $i <= $quantity; $i++) {
            $generated++;
            $candidateText = "Formational study question #{$i} for Topic {$topicId}: What is the Catholic doctrinal teaching on this objective?";

            $hash = $duplicateService->generateSimilarityHash($candidateText);
            if (QuestionBankItem::where('duplicate_similarity_hash', $hash)->exists()) {
                $duplicates++;
                continue;
            }

            try {
                $questionBankService->createQuestion([
                    'track_id' => $trackId,
                    'topic_id' => $topicId,
                    'level_id' => $levelId,
                    'question_type' => 'MULTIPLE_CHOICE',
                    'question_text' => $candidateText,
                    'explanation' => 'Official catechetical explanation referencing Church teaching.',
                    'reference_citation' => 'CCC 1213',
                    'editorial_difficulty' => 'MEDIUM',
                    'status' => 'AI_GENERATED',
                ], [
                    ['option_key' => 'A', 'option_text' => 'Correct doctrinal answer statement', 'is_correct' => true],
                    ['option_key' => 'B', 'option_text' => 'Common misconception option', 'is_correct' => false],
                    ['option_key' => 'C', 'option_text' => 'Plausible historical distractor', 'is_correct' => false],
                    ['option_key' => 'D', 'option_text' => 'Secular alternative distractor', 'is_correct' => false],
                ], $jobRecord->initiator);

                $accepted++;
            } catch (\Throwable $e) {
                $failed++;
                Log::error("GenerateContentBatchJob failure: " . $e->getMessage());
            }

            // Periodic database progress flush
            if ($i % 10 === 0 || $i === $quantity) {
                $jobRecord->update([
                    'generated_count' => $generated,
                    'accepted_count' => $accepted,
                    'duplicate_count' => $duplicates,
                    'failed_count' => $failed,
                ]);
            }
        }

        $jobRecord->update([
            'status' => 'COMPLETED',
            'completed_at' => now(),
            'generated_count' => $generated,
            'accepted_count' => $accepted,
            'duplicate_count' => $duplicates,
            'failed_count' => $failed,
        ]);
    }
}
