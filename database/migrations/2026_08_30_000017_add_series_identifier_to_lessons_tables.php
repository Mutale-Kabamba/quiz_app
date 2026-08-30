<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('series_identifier')->nullable()->after('category_id')->index();
            $table->string('series_title')->nullable()->after('series_identifier');
            $table->unsignedSmallInteger('series_order')->nullable()->default(1)->after('series_title');
            $table->boolean('is_progressive')->default(true)->after('series_order');
            $table->index(['series_identifier', 'series_order']);
        });

        Schema::table('micro_lessons', function (Blueprint $table) {
            $table->string('series_identifier')->nullable()->after('category_id')->index();
            $table->string('series_title')->nullable()->after('series_identifier');
            $table->unsignedSmallInteger('series_order')->nullable()->default(1)->after('series_title');
            $table->boolean('is_progressive')->default(true)->after('series_order');
            $table->index(['series_identifier', 'series_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['series_identifier', 'series_order']);
            $table->dropColumn(['series_identifier', 'series_title', 'series_order', 'is_progressive']);
        });

        Schema::table('micro_lessons', function (Blueprint $table) {
            $table->dropIndex(['series_identifier', 'series_order']);
            $table->dropColumn(['series_identifier', 'series_title', 'series_order', 'is_progressive']);
        });
    }
};
