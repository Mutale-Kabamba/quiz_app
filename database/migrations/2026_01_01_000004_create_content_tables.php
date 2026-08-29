<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->unsignedTinyInteger('level')->default(1);
            $table->text('question_text');
            $table->json('options');
            $table->string('correct_option_key', 10);
            $table->text('explanation')->nullable();
            $table->string('reference_citation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'level', 'is_active']);
        });

        Schema::create('study_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('subheading')->nullable();
            $table->longText('content_body');
            $table->string('reference_code')->nullable();
            $table->string('downloadable_pdf_url')->nullable();
            $table->unsignedSmallInteger('estimated_read_minutes')->default(5);
            $table->timestamps();

            $table->index(['category_id', 'reference_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_notes');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('categories');
    }
};
