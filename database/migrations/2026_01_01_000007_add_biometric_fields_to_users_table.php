<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('biometric_token_hash')->nullable()->after('remember_token');
            $table->timestamp('biometric_enabled_at')->nullable()->after('biometric_token_hash');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['biometric_token_hash', 'biometric_enabled_at']);
        });
    }
};
