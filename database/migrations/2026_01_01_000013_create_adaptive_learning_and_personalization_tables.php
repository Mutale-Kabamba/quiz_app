<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adaptive Learning & Topic Mastery
        Schema::create('user_topic_masteries', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('taxonomy_topics')->cascadeOnDelete();
            $table->unsignedTinyInteger('mastery_score')->default(0); // 0 to 100%
            $table->enum('confidence_level', ['LOW', 'MEDIUM', 'HIGH'])->default('LOW');
            $table->unsignedInteger('questions_attempted')->default(0);
            $table->unsignedInteger('questions_correct')->default(0);
            $table->unsignedInteger('lessons_completed')->default(0);
            $table->json('weak_concept_ids')->nullable(); // Concept IDs needing reinforcement
            $table->json('strong_concept_ids')->nullable(); // Mastered concepts
            $table->timestamp('last_assessed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'topic_id']);
            $table->index(['user_id', 'mastery_score']);
        });

        // 2. Learning Paths & Structured Journeys
        Schema::create('learning_paths', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g. "Catholic Faith Foundations", "The Sacraments Deep Dive"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('track_id')->nullable()->constrained('taxonomy_tracks')->nullOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('formation_levels')->nullOnDelete();
            $table->unsignedSmallInteger('estimated_total_hours')->default(5);
            $table->unsignedInteger('xp_completion_bonus')->default(150);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('learning_path_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->constrained('learning_paths')->cascadeOnDelete();
            $table->unsignedSmallInteger('step_number')->default(1);
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignUuid('study_resource_id')->nullable()->constrained('study_resources')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('taxonomy_topics')->nullOnDelete();
            $table->foreignId('quiz_blueprint_id')->nullable()->constrained('quiz_blueprints')->nullOnDelete();
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(['learning_path_id', 'step_number']);
        });

        // 3. Structured Study Plans (7, 14, 30, 90 Days)
        Schema::create('study_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g. "30-Day Catholic Foundations"
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('duration_days')->default(30);
            $table->text('description')->nullable();
            $table->json('daily_schedule_manifest'); // [{day: 1, topic_id: 10, scripture_ref: "Rom 12:2", resource_id: uuid}]
            $table->unsignedInteger('completion_xp_reward')->default(300);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Downloadable Content Packs for Offline Use
        Schema::create('content_packs', function (Blueprint $table) {
            $table->id();
            $table->string('pack_name'); // e.g. "YOUCAT Beginner Formation Pack"
            $table->string('pack_code')->unique(); // "PACK_YOUCAT_BEGINNER_V1"
            $table->text('description')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->string('checksum_hash', 64)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->json('included_resource_ids'); // Array of StudyResource UUIDs
            $table->json('included_question_ids'); // Array of QuestionBankItem UUIDs
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 5. User Private Notes (Explicitly segregated from official doctrine)
        Schema::create('user_personal_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->longText('note_body');
            $table->foreignUuid('study_resource_id')->nullable()->constrained('study_resources')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('taxonomy_topics')->nullOnDelete();
            $table->string('scripture_reference_tag')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // 6. User Study Collections (Custom Folders)
        Schema::create('user_study_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('collection_name'); // "My Confirmation Revision"
            $table->string('color_tag', 20)->default('blue');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'collection_name']);
        });

        Schema::create('user_study_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('user_study_collections')->cascadeOnDelete();
            $table->string('item_type'); // "StudyResource", "QuestionBankItem", "Flashcard"
            $table->string('item_id');
            $table->string('custom_notes')->nullable();
            $table->timestamps();

            $table->unique(['collection_id', 'item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_study_collection_items');
        Schema::dropIfExists('user_study_collections');
        Schema::dropIfExists('user_personal_notes');
        Schema::dropIfExists('content_packs');
        Schema::dropIfExists('study_plans');
        Schema::dropIfExists('learning_path_steps');
        Schema::dropIfExists('learning_paths');
        Schema::dropIfExists('user_topic_masteries');
    }
};
