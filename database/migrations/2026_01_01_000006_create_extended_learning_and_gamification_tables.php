<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend Users table with XP, Level and Longest Streak
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('xp')->default(0)->after('status');
            $table->unsignedSmallInteger('level')->default(1)->after('xp');
            $table->unsignedInteger('longest_streak')->default(0)->after('current_streak');
        });

        // 2. Structured Lessons Table
        Schema::create('lessons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subheading')->nullable();
            $table->json('summary_takeaways')->nullable(); // Array of 3-5 bullet points
            $table->json('content_sections')->nullable(); // Structured sections: [{heading, body, callout, scripture_quote, catechism_quote}]
            $table->json('key_terms')->nullable(); // Key terms & vocabulary [{term, definition}]
            $table->unsignedSmallInteger('estimated_read_minutes')->default(5);
            $table->unsignedTinyInteger('difficulty')->default(1); // 1: Junior, 2: Youth, 3: Advanced
            $table->string('scripture_citations')->nullable(); // e.g. "Romans 6:3-4, Matthew 28:19"
            $table->string('catechism_citations')->nullable(); // e.g. "CCC 1213-1216, YOUCAT 194"
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->timestamps();

            $table->index(['category_id', 'status', 'display_order']);
        });

        // 3. User Lesson Progress
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_bookmarked')->default(false);
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });

        // 4. Flashcards Table
        Schema::create('flashcards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignUuid('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->text('front_text'); // Question / Prompt
            $table->text('back_text'); // Answer / Doctrinal summary
            $table->string('reference_citation')->nullable(); // e.g. "CCC #1213"
            $table->unsignedTinyInteger('difficulty')->default(1);
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamps();

            $table->index(['category_id', 'lesson_id', 'status']);
        });

        // 5. User Flashcard Spaced Reviews
        Schema::create('flashcard_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('flashcard_id')->constrained('flashcards')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->default(2); // 1: Again, 2: Good, 3: Easy
            $table->timestamp('reviewed_at');
            $table->date('next_review_at');
            $table->unsignedSmallInteger('review_count')->default(1);
            $table->timestamps();

            $table->index(['user_id', 'next_review_at']);
            $table->unique(['user_id', 'flashcard_id']);
        });

        // 6. Daily Challenges Table
        Schema::create('daily_challenges', function (Blueprint $table) {
            $table->id();
            $table->date('challenge_date')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('question_ids'); // List of 5 Question UUIDs
            $table->unsignedInteger('xp_reward')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['challenge_date', 'is_active']);
        });

        // 7. User Challenge Participations
        Schema::create('user_challenge_participations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('daily_challenge_id')->constrained('daily_challenges')->cascadeOnDelete();
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('xp_earned')->default(50);
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->unique(['user_id', 'daily_challenge_id']);
        });

        // 8. Achievements Definition Table
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('icon')->default('🏆');
            $table->enum('type', ['streak', 'quiz_count', 'lesson_count', 'flashcard_count', 'xp_total', 'accuracy'])->default('streak');
            $table->unsignedInteger('threshold')->default(1);
            $table->unsignedInteger('xp_reward')->default(100);
            $table->timestamps();
        });

        // 9. User Unlocked Achievements
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained('achievements')->cascadeOnDelete();
            $table->timestamp('unlocked_at');
            $table->timestamps();

            $table->unique(['user_id', 'achievement_id']);
        });

        // 10. Quiz Attempt Individual Answers (For Learning Intelligence & Weak-Area Detection)
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('quiz_attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignUuid('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('selected_option_key', 10)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->index(['quiz_attempt_id', 'category_id', 'is_correct']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('user_challenge_participations');
        Schema::dropIfExists('daily_challenges');
        Schema::dropIfExists('flashcard_reviews');
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('lessons');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'level', 'longest_streak']);
        });
    }
};
