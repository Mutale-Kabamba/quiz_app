<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Parish Events Table
        Schema::create('parish_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('parish_id')->constrained('parishes')->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('event_type')->default('youth_meeting'); // youth_meeting, bible_study, catechesis, quiz_practice, retreat, rally_prep, competition
            $table->date('event_date');
            $table->string('start_time')->nullable(); // e.g. "14:00"
            $table->string('end_time')->nullable(); // e.g. "16:30"
            $table->string('location')->nullable(); // e.g. "Parish Hall"
            $table->string('organizer')->nullable();
            $table->boolean('requires_registration')->default(false);
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->enum('status', ['draft', 'published', 'completed', 'cancelled'])->default('published');
            $table->timestamps();

            $table->index(['parish_id', 'event_date', 'status']);
        });

        // 2. Parish Announcements Table
        Schema::create('parish_announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('parish_id')->constrained('parishes')->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('target_type', ['all', 'selected'])->default('all');
            $table->enum('priority', ['normal', 'urgent', 'celebration'])->default('normal');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['parish_id', 'created_at']);
        });

        // 3. Parish Transfer Requests Table
        Schema::create('parish_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_parish_id')->constrained('parishes')->cascadeOnDelete();
            $table->foreignId('to_parish_id')->constrained('parishes')->cascadeOnDelete();
            $table->foreignUuid('requested_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['from_parish_id', 'status']);
            $table->index(['to_parish_id', 'status']);
        });

        // 4. Parish Competitions & Rally Sessions Table
        Schema::create('parish_competitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('parish_id')->constrained('parishes')->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('rally_pin', 6)->nullable()->index();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->unsignedTinyInteger('level')->default(1);
            $table->unsignedSmallInteger('time_limit_seconds')->default(15);
            $table->unsignedSmallInteger('question_count')->default(10);
            $table->enum('status', ['draft', 'scheduled', 'active', 'completed', 'cancelled'])->default('scheduled');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->timestamps();

            $table->index(['parish_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parish_competitions');
        Schema::dropIfExists('parish_transfers');
        Schema::dropIfExists('parish_announcements');
        Schema::dropIfExists('parish_events');
    }
};
