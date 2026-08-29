<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Micro-Learning ("Learn in 5 Minutes") Table
        Schema::create('micro_lessons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('topic_id')->nullable()->constrained('taxonomy_topics')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('hook_question')->nullable(); // e.g. "What are the 4 Marks of the True Church?"
            $table->longText('content_body');
            $table->json('takeaways')->nullable(); // 3-4 concise points
            $table->json('flashcard_ids')->nullable(); // Exactly 3 flashcard UUIDs
            $table->json('question_ids')->nullable(); // Exactly 3-5 Question UUIDs
            $table->string('reference_citation')->nullable(); // CCC / Scripture
            $table->unsignedSmallInteger('read_time_minutes')->default(4);
            $table->unsignedInteger('xp_reward')->default(35);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['topic_id', 'is_published']);
        });

        // 2. User Micro-Lesson Progress & Completion
        Schema::create('user_micro_lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('micro_lesson_id')->constrained('micro_lessons')->cascadeOnDelete();
            $table->unsignedTinyInteger('quiz_score')->default(0);
            $table->unsignedTinyInteger('quiz_total')->default(3);
            $table->unsignedInteger('xp_earned')->default(35);
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->unique(['user_id', 'micro_lesson_id']);
        });

        // 3. User Spaced Question & Concept Reviews (Intelligent Spaced Repetition)
        Schema::create('user_spaced_question_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('question_id')->nullable(); // Question UUID
            $table->foreignId('concept_id')->nullable()->constrained('taxonomy_concepts')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('taxonomy_topics')->nullOnDelete();
            $table->unsignedSmallInteger('mistake_count')->default(1);
            $table->unsignedSmallInteger('consecutive_correct')->default(0);
            $table->unsignedSmallInteger('interval_days')->default(1); // 1, 3, 7, 14, 30
            $table->timestamp('last_reviewed_at')->nullable();
            $table->date('next_review_date');
            $table->boolean('is_mastered')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'next_review_date', 'is_mastered']);
        });

        // 4. Rally Preparation Programs ("Prepare for the Rally")
        Schema::create('rally_preparations', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // "2026 Livingstone Diocesan Youth Rally Prep"
            $table->string('slug')->unique();
            $table->date('rally_date');
            $table->text('description')->nullable();
            $table->unsignedInteger('target_questions_count')->default(200);
            $table->json('domain_weights')->nullable(); // {"scripture": 25, "catechism": 30, "history": 15, "saints": 15, "doctrine": 15}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. User Rally Readiness Tracker
        Schema::create('user_rally_readiness', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rally_id')->constrained('rally_preparations')->cascadeOnDelete();
            $table->unsignedTinyInteger('overall_readiness_percentage')->default(0);
            $table->unsignedTinyInteger('scripture_readiness')->default(0);
            $table->unsignedTinyInteger('catechism_readiness')->default(0);
            $table->unsignedTinyInteger('history_readiness')->default(0);
            $table->unsignedTinyInteger('saints_readiness')->default(0);
            $table->unsignedTinyInteger('doctrine_readiness')->default(0);
            $table->unsignedInteger('training_questions_answered')->default(0);
            $table->timestamp('last_trained_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'rally_id']);
        });

        // 6. Parish Community & Inter-Parish Formation Challenges
        Schema::create('parish_formation_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parish_id')->constrained('parishes')->cascadeOnDelete();
            $table->foreignId('challenger_parish_id')->nullable()->constrained('parishes')->nullOnDelete();
            $table->string('title'); // e.g. "St. Theresa vs St. Joseph Lenten Formation Challenge"
            $table->text('description')->nullable();
            $table->foreignId('topic_id')->nullable()->constrained('taxonomy_topics')->nullOnDelete();
            $table->foreignId('track_id')->nullable()->constrained('taxonomy_tracks')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('target_mastery_percentage')->default(70);
            $table->unsignedInteger('target_youth_count')->default(20);
            $table->unsignedInteger('xp_reward_pool')->default(1000);
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();

            $table->index(['parish_id', 'status']);
        });

        // 7. User Parish Challenge Participation Entries
        Schema::create('user_parish_challenge_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('parish_formation_challenges')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parish_id')->constrained('parishes')->cascadeOnDelete();
            $table->unsignedInteger('contribution_xp')->default(0);
            $table->unsignedSmallInteger('tasks_completed')->default(0);
            $table->boolean('has_claimed_reward')->default(false);
            $table->timestamps();

            $table->unique(['challenge_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_parish_challenge_entries');
        Schema::dropIfExists('parish_formation_challenges');
        Schema::dropIfExists('user_rally_readiness');
        Schema::dropIfExists('rally_preparations');
        Schema::dropIfExists('user_spaced_question_reviews');
        Schema::dropIfExists('user_micro_lesson_completions');
        Schema::dropIfExists('micro_lessons');
    }
};
