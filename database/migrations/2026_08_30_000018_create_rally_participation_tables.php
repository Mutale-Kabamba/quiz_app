<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance diocesan_competitions table with scope & registration window fields
        Schema::table('diocesan_competitions', function (Blueprint $table) {
            if (!Schema::hasColumn('diocesan_competitions', 'scope_type')) {
                $table->string('scope_type')->default('diocese')->after('competition_type'); // diocese, deanery, parish, custom
            }
            if (!Schema::hasColumn('diocesan_competitions', 'registration_open_at')) {
                $table->dateTime('registration_open_at')->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('diocesan_competitions', 'registration_close_at')) {
                $table->dateTime('registration_close_at')->nullable()->after('registration_open_at');
            }
            if (!Schema::hasColumn('diocesan_competitions', 'join_requests_enabled')) {
                $table->boolean('join_requests_enabled')->default(true)->after('registration_close_at');
            }
            if (!Schema::hasColumn('diocesan_competitions', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('join_requests_enabled');
            }
            if (!Schema::hasColumn('diocesan_competitions', 'max_participants')) {
                $table->integer('max_participants')->nullable()->after('is_public');
            }
        });

        // 2. Create rally_participants table
        Schema::create('rally_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rally_id')->constrained('diocesan_competitions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('access_code')->nullable()->unique();
            $table->string('status')->default('approved'); // pending, approved, rejected, active, completed, removed
            $table->dateTime('joined_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('completed_at')->nullable();
            $table->integer('score')->default(0);
            $table->integer('rank')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['rally_id', 'user_id']);
            $table->index(['rally_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // 3. Create rally_join_requests table
        Schema::create('rally_join_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rally_id')->constrained('diocesan_competitions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->text('message')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['rally_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rally_join_requests');
        Schema::dropIfExists('rally_participants');

        Schema::table('diocesan_competitions', function (Blueprint $table) {
            $columns = [
                'scope_type',
                'registration_open_at',
                'registration_close_at',
                'join_requests_enabled',
                'is_public',
                'max_participants',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('diocesan_competitions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
