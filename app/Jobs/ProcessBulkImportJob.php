<?php

namespace App\Jobs;

use App\Models\ContentImportJob;
use App\Services\ContentImportExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBulkImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $jobId;
    public array $rowsChunk;

    public function __construct(string $jobId, array $rowsChunk)
    {
        $this->jobId = $jobId;
        $this->rowsChunk = $rowsChunk;
    }

    public function handle(): void
    {
        $jobRecord = ContentImportJob::find($this->jobId);
        if (!$jobRecord) {
            return;
        }

        $jobRecord->update(['status' => 'PROCESSING', 'started_at' => $jobRecord->started_at ?? now()]);

        app(ContentImportExportService::class)->importQuestionsChunk($jobRecord, $this->rowsChunk, $jobRecord->uploader);

        if ($jobRecord->processed_rows >= $jobRecord->total_rows) {
            $jobRecord->update(['status' => 'COMPLETED', 'completed_at' => now()]);
        }
    }
}
