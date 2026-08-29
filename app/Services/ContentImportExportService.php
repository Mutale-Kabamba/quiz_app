<?php

namespace App\Services;

use App\Models\ContentImportJob;
use App\Models\QuestionBankItem;
use App\Models\StudyResource;
use App\Models\TaxonomyTrack;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContentImportExportService
{
    /**
     * Process a chunk of question records for an import job
     */
    public function importQuestionsChunk(ContentImportJob $job, array $rows, ?User $uploader = null): void
    {
        $processed = (int) ($job->processed_rows ?? 0);
        $successful = (int) ($job->successful_rows ?? 0);
        $duplicates = (int) ($job->duplicate_rows ?? 0);
        $failed = (int) ($job->failed_rows ?? 0);
        $errors = $job->validation_errors ?? [];

        $duplicateService = app(DuplicateDetectionService::class);
        $questionBankService = app(QuestionBankService::class);

        foreach ($rows as $rowIndex => $row) {
            $processed++;

            // Basic Validation
            if (empty($row['question_text']) || empty($row['track_id'])) {
                $failed++;
                $errors[] = ['row' => $processed, 'error' => 'Missing question_text or track_id'];
                continue;
            }

            // Duplicate Check
            $hash = $duplicateService->generateSimilarityHash($row['question_text']);
            $existing = QuestionBankItem::where('duplicate_similarity_hash', $hash)->exists();
            if ($existing) {
                $duplicates++;
                continue;
            }

            try {
                $options = $row['options'] ?? [
                    ['option_key' => 'A', 'option_text' => $row['option_a'] ?? 'Option A', 'is_correct' => ($row['correct_option'] ?? 'A') === 'A'],
                    ['option_key' => 'B', 'option_text' => $row['option_b'] ?? 'Option B', 'is_correct' => ($row['correct_option'] ?? 'A') === 'B'],
                    ['option_key' => 'C', 'option_text' => $row['option_c'] ?? 'Option C', 'is_correct' => ($row['correct_option'] ?? 'A') === 'C'],
                    ['option_key' => 'D', 'option_text' => $row['option_d'] ?? 'Option D', 'is_correct' => ($row['correct_option'] ?? 'A') === 'D'],
                ];

                $questionBankService->createQuestion([
                    'track_id' => (int) $row['track_id'],
                    'category_id' => $row['category_id'] ?? null,
                    'topic_id' => $row['topic_id'] ?? null,
                    'question_type' => $row['question_type'] ?? 'MULTIPLE_CHOICE',
                    'question_text' => $row['question_text'],
                    'explanation' => $row['explanation'] ?? null,
                    'reference_citation' => $row['reference_citation'] ?? null,
                    'editorial_difficulty' => $row['editorial_difficulty'] ?? 'MEDIUM',
                    'status' => 'IMPORTED',
                ], $options, $uploader);

                $successful++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = ['row' => $processed, 'error' => $e->getMessage()];
            }
        }

        $job->update([
            'processed_rows' => $processed,
            'successful_rows' => $successful,
            'duplicate_rows' => $duplicates,
            'failed_rows' => $failed,
            'validation_errors' => $errors,
        ]);
    }

    /**
     * Export questions by track or status to an array of rows
     */
    public function exportQuestions(?int $trackId = null, string $status = 'PUBLISHED', int $limit = 5000): array
    {
        $query = QuestionBankItem::where('status', $status)->with(['options', 'track', 'topic']);
        if ($trackId) {
            $query->where('track_id', $trackId);
        }

        return $query->limit($limit)->get()->map(function ($q) {
            $options = $q->options->pluck('option_text', 'option_key')->toArray();
            $correctOption = $q->options->firstWhere('is_correct', true)?->option_key ?? 'A';

            return [
                'id' => $q->id,
                'track' => $q->track?->name,
                'topic' => $q->topic?->name,
                'question_type' => $q->question_type,
                'question_text' => $q->question_text,
                'option_a' => $options['A'] ?? '',
                'option_b' => $options['B'] ?? '',
                'option_c' => $options['C'] ?? '',
                'option_d' => $options['D'] ?? '',
                'correct_option' => $correctOption,
                'explanation' => $q->explanation,
                'reference' => $q->reference_citation,
                'difficulty' => $q->editorial_difficulty,
            ];
        })->toArray();
    }
}
