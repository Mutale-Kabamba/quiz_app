<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'biometric_token_hash')) {
                $table->string('biometric_token_hash')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'biometric_credential_id')) {
                $table->string('biometric_credential_id')->nullable()->after('biometric_token_hash')->index();
            }
            if (!Schema::hasColumn('users', 'biometric_enabled_at')) {
                $table->timestamp('biometric_enabled_at')->nullable()->after('biometric_credential_id');
            }
        });
    }

    public function down(): void
    {
        // Safe no-op
    }
};
