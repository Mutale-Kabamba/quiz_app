<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deaneries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 10)->unique();
            $table->timestamps();
        });

        Schema::create('parishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deanery_id')->constrained('deaneries')->cascadeOnDelete();
            $table->string('name');
            $table->string('location')->nullable();
            $table->timestamps();

            $table->unique(['deanery_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parishes');
        Schema::dropIfExists('deaneries');
    }
};
