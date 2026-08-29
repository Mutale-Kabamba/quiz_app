<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Asynchronous Content Generation Batches
        Schema::create('content_generation_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('job_title');
            $table->foreignId('track_id')->nullable()->constrained('taxonomy_tracks')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('taxonomy_categories')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('taxonomy_topics')->nullOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('formation_levels')->nullOnDelete();
            $table->string('content_kind')->default('QUESTIONS'); // "QUESTIONS", "STUDY_NOTES", "FLASHCARDS"
            $table->unsignedInteger('requested_quantity')->default(50);
            $table->unsignedInteger('generated_count')->default(0);
            $table->unsignedInteger('accepted_count')->default(0);
            $table->unsignedInteger('duplicate_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->enum('status', ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED', 'CANCELLED'])->default('PENDING');
            $table->json('generation_parameters')->nullable(); // Prompts, constraints, source references
            $table->json('execution_log')->nullable();
            $table->foreignUuid('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'content_kind']);
        });

        // 2. Memory-Safe Bulk Import Batches
        Schema::create('content_import_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('file_name');
            $table->enum('format', ['CSV', 'EXCEL', 'JSON', 'JSONL'])->default('CSV');
            $table->enum('target_entity', ['QUESTIONS', 'STUDY_RESOURCES', 'FLASHCARDS', 'TAXONOMY'])->default('QUESTIONS');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('successful_rows')->default(0);
            $table->unsignedInteger('duplicate_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->enum('status', ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED'])->default('PENDING');
            $table->json('validation_errors')->nullable(); // [{row, error, payload}]
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'target_entity']);
        });

        // 3. Editorial & Theological Review Logs
        Schema::create('content_review_logs', function (Blueprint $table) {
            $table->id();
            $table->string('reviewable_type'); // "StudyResource" or "QuestionBankItem"
            $table->string('reviewable_id');
            $table->foreignUuid('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('action', [
                'SUBMITTED_FOR_REVIEW',
                'APPROVED',
                'REJECTED',
                'REQUESTED_REVISION',
                'PUBLISHED',
                'ARCHIVED'
            ]);
            $table->unsignedTinyInteger('theological_accuracy_rating')->default(5); // 1 to 5
            $table->unsignedTinyInteger('clarity_rating')->default(5); // 1 to 5
            $table->text('reviewer_comments')->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id']);
        });

        // 4. Content Reports from Youth & Parish Admins
        Schema::create('content_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reportable_type'); // "QuestionBankItem", "StudyResource", "Flashcard"
            $table->string('reportable_id');
            $table->enum('reason_category', [
                'WRONG_ANSWER',
                'UNCLEAR_WORDING',
                'WRONG_REFERENCE',
                'TYPO',
                'DUPLICATE',
                'DOCTRINAL_CONCERN',
                'TECHNICAL_ISSUE',
                'OTHER'
            ])->default('WRONG_ANSWER');
            $table->text('description');
            $table->enum('status', ['PENDING', 'IN_REVIEW', 'RESOLVED', 'DISMISSED'])->default('PENDING');
            $table->text('resolution_notes')->nullable();
            $table->foreignUuid('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'reason_category']);
            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_reports');
        Schema::dropIfExists('content_review_logs');
        Schema::dropIfExists('content_import_jobs');
        Schema::dropIfExists('content_generation_jobs');
    }
};
