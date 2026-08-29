<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Universal Scalable Question Bank
        Schema::create('question_bank_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('track_id')->constrained('taxonomy_tracks')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('taxonomy_categories')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('taxonomy_topics')->nullOnDelete();
            $table->foreignId('subtopic_id')->nullable()->constrained('taxonomy_subtopics')->nullOnDelete();
            $table->foreignId('concept_id')->nullable()->constrained('taxonomy_concepts')->nullOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('formation_levels')->nullOnDelete();
            $table->foreignId('age_band_id')->nullable()->constrained('age_bands')->nullOnDelete();
            $table->foreignId('bloom_id')->nullable()->constrained('bloom_taxonomies')->nullOnDelete();

            $table->enum('question_type', [
                'MULTIPLE_CHOICE',
                'TRUE_FALSE',
                'MULTIPLE_SELECT',
                'FILL_IN_THE_BLANK',
                'MATCHING',
                'ORDERING',
                'SEQUENCE',
                'SHORT_ANSWER',
                'SCENARIO',
                'CASE_STUDY',
                'IMAGE_BASED',
                'SCRIPTURE_REFERENCE',
                'QUOTE_IDENTIFICATION',
                'PERSON_IDENTIFICATION',
                'EVENT_IDENTIFICATION',
                'TERM_DEFINITION'
            ])->default('MULTIPLE_CHOICE');

            $table->text('question_text');
            $table->text('explanation')->nullable();
            $table->text('teaching_point')->nullable();
            $table->string('correct_answer_payload')->nullable(); // For TRUE_FALSE, SHORT_ANSWER, or primary correct key
            $table->string('reference_citation')->nullable(); // e.g. "CCC 1213", "Luke 1:28"
            $table->string('image_url')->nullable();
            $table->string('language_code', 10)->default('en');

            // Difficulty & Item Statistics
            $table->enum('editorial_difficulty', [
                'VERY_EASY',
                'EASY',
                'MEDIUM',
                'HARD',
                'VERY_HARD',
                'EXPERT'
            ])->default('MEDIUM');

            $table->float('empirical_difficulty')->nullable(); // 0.0 (Hardest) to 1.0 (Easiest) based on actual accuracy
            $table->float('discrimination_index')->nullable(); // Item discrimination factor
            $table->unsignedInteger('times_served')->default(0);
            $table->unsignedInteger('times_answered')->default(0);
            $table->unsignedInteger('times_correct')->default(0);
            $table->unsignedInteger('times_incorrect')->default(0);
            $table->float('avg_response_time_seconds')->default(0.0);
            $table->unsignedSmallInteger('health_score')->default(100); // 0 to 100 quality score

            // Duplicate & Variant Management
            $table->string('duplicate_similarity_hash', 64)->nullable()->index();
            $table->foreignUuid('parent_question_id')->nullable()->references('id')->on('question_bank_items')->nullOnDelete();
            $table->string('duplicate_cluster_id', 64)->nullable()->index();

            // Governance & Review Lifecycle
            $table->enum('status', [
                'DRAFT',
                'AI_GENERATED',
                'IMPORTED',
                'UNDER_REVIEW',
                'NEEDS_REVISION',
                'APPROVED',
                'PUBLISHED',
                'ARCHIVED',
                'REJECTED'
            ])->default('DRAFT');

            $table->boolean('is_competition_eligible')->default(true);
            $table->boolean('is_practice_eligible')->default(true);
            $table->foreignUuid('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('theological_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('current_version')->default(1);
            $table->timestamps();

            $table->index(['track_id', 'status', 'level_id']);
            $table->index(['topic_id', 'status', 'editorial_difficulty']);
            $table->index(['concept_id', 'status']);
            $table->index(['question_type', 'status']);
        });

        // 2. Question Options (Normalized for MCQ, Multiple Select, Ordering, Matching)
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('question_bank_item_id')->constrained('question_bank_items')->cascadeOnDelete();
            $table->string('option_key', 10); // "A", "B", "C", "D"
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->text('explanation_why_incorrect')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['question_bank_item_id', 'option_key']);
        });

        // 3. Question Version History
        Schema::create('question_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('question_bank_item_id')->constrained('question_bank_items')->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->text('question_text');
            $table->json('options_snapshot'); // Structured snapshot of options
            $table->string('correct_answer_snapshot');
            $table->text('explanation_snapshot')->nullable();
            $table->string('reference_citation_snapshot')->nullable();
            $table->string('changelog_notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['question_bank_item_id', 'version_number']);
        });

        // 4. Question Pools (Named Sets e.g. "YOUCAT Beginner", "Scripture Rally Pool")
        Schema::create('question_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('track_id')->nullable()->constrained('taxonomy_tracks')->nullOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('formation_levels')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('question_pool_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_pool_id')->constrained('question_pools')->cascadeOnDelete();
            $table->foreignUuid('question_bank_item_id')->constrained('question_bank_items')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['question_pool_id', 'question_bank_item_id']);
        });

        // 5. Quiz Blueprints (Rule-based dynamic generation templates)
        Schema::create('quiz_blueprints', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('question_count')->default(10);
            $table->unsignedSmallInteger('time_limit_seconds')->default(180);
            $table->foreignId('level_id')->nullable()->constrained('formation_levels')->nullOnDelete();
            $table->json('track_rules')->nullable(); // [{track_id, count, percentage}]
            $table->json('difficulty_distribution')->nullable(); // {EASY: 30, MEDIUM: 50, HARD: 20}
            $table->json('bloom_distribution')->nullable(); // {remember: 40, understand: 40, apply: 20}
            $table->unsignedTinyInteger('unseen_question_ratio')->default(70); // 70% unseen, 30% revision/spaced
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Tournament / Competition Locked Question Sets (Guarantees zero question drift)
        Schema::create('competition_locked_question_sets', function (Blueprint $table) {
            $table->id();
            $table->string('competition_identifier')->index(); // Links to DiocesanCompetition or ParishCompetition
            $table->string('round_name'); // "Round 1", "Quarter Finals", "Grand Finale"
            $table->unsignedSmallInteger('round_number')->default(1);
            $table->json('locked_question_snapshots'); // Array of full question snapshots at lock time
            $table->unsignedSmallInteger('question_count');
            $table->boolean('is_locked')->default(true);
            $table->foreignUuid('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at');
            $table->timestamps();

            $table->index(['competition_identifier', 'round_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_locked_question_sets');
        Schema::dropIfExists('quiz_blueprints');
        Schema::dropIfExists('question_pool_items');
        Schema::dropIfExists('question_pools');
        Schema::dropIfExists('question_versions');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('question_bank_items');
    }
};
