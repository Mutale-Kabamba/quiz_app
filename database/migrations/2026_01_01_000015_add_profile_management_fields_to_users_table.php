<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('rejection_reason');
            $table->date('dob')->nullable()->after('avatar_path');
            $table->string('gender', 20)->nullable()->after('dob');
            $table->json('preferences')->nullable()->after('gender');
            $table->timestamp('last_password_changed_at')->nullable()->after('preferences');
            $table->timestamp('deactivated_at')->nullable()->after('last_password_changed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_path',
                'dob',
                'gender',
                'preferences',
                'last_password_changed_at',
                'deactivated_at',
            ]);
        });
    }
};
