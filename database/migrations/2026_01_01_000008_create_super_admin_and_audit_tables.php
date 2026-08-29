<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. AUDIT LOGS (Immutable record of administrative actions)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // e.g. user_suspended, parish_created, content_approved, score_corrected
            $table->string('entity_type')->nullable(); // Model class
            $table->string('entity_id')->nullable(); // Model ID
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['action', 'created_at']);
            $table->index('user_id');
        });

        // 2. XP TRANSACTIONS (Authoritative financial-style ledger for XP)
        Schema::create('xp_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('amount'); // e.g. +20, +50, -20
            $table->string('source_type'); // lesson_completed, quiz_passed, daily_challenge, flashcard_review, admin_adjustment
            $table->string('source_id')->nullable(); // ID of the lesson, quiz, challenge, etc.
            $table->string('description');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('source_type');
        });

        // 3. SYSTEM SETTINGS (Centralized configuration)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->string('group')->default('general'); // general, gamification, anti_cheating, notifications
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 4. DIOCESAN COMPETITIONS (Cross-deanery, diocesan, and youth rally events)
        Schema::create('diocesan_competitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('competition_type')->default('diocesan'); // diocesan, deanery, parish, youth_rally
            $table->foreignId('deanery_id')->nullable()->constrained('deaneries')->nullOnDelete();
            $table->foreignId('parish_id')->nullable()->constrained('parishes')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('rally_pin', 10)->nullable()->unique();
            $table->integer('level')->default(1);
            $table->integer('time_limit_seconds')->default(15);
            $table->integer('question_count')->default(15);
            $table->string('status')->default('scheduled'); // draft, scheduled, live, paused, completed, cancelled
            $table->json('scoring_rules')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->timestamps();

            $table->index(['competition_type', 'status']);
        });

        // 5. ENHANCE PARISHES TABLE WITH STATUS & CONTACT INFO IF MISSING
        Schema::table('parishes', function (Blueprint $table) {
            if (!Schema::hasColumn('parishes', 'code')) {
                $table->string('code')->nullable()->after('name');
            }
            if (!Schema::hasColumn('parishes', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('location');
            }
            if (!Schema::hasColumn('parishes', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('parishes', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('contact_email');
            }
            if (!Schema::hasColumn('parishes', 'description')) {
                $table->text('description')->nullable()->after('contact_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diocesan_competitions');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('xp_transactions');
        Schema::dropIfExists('audit_logs');
    }
};
