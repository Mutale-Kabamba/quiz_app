<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->unsignedTinyInteger('level')->default(1);
            $table->enum('mode', ['practice', 'ranked'])->default('ranked');
            $table->unsignedInteger('score')->default(0);
            $table->unsignedSmallInteger('total_questions')->default(0);
            $table->unsignedSmallInteger('correct_answers_count')->default(0);
            $table->unsignedInteger('time_taken_seconds')->default(0);
            $table->timestamp('completed_at');
            $table->boolean('is_synced')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'category_id', 'mode', 'score']);
        });

        Schema::create('question_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('issue_type', ['wrong_answer', 'typo', 'bad_reference', 'other'])->default('other');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending');
            $table->timestamps();

            $table->index(['status', 'issue_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_reports');
        Schema::dropIfExists('quiz_attempts');
    }
};
