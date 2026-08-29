<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Authoritative Catholic Sources
        Schema::create('catholic_sources', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g. "Catechism of the Catholic Church", "The Holy Bible (RSV-CE)", "YOUCAT", "DOCAT"
            $table->string('short_code', 30)->unique(); // "CCC", "RSVCE", "YOUCAT", "DOCAT", "VATICAN2", "CANON_LAW"
            $table->string('publisher_authority'); // "Holy See", "USCCB", "ZEC (Zambia Episcopal Conference)", "Livingstone Diocese"
            $table->enum('document_type', [
                'SCRIPTURE',
                'CATECHISM',
                'COUNCIL_DOCUMENT',
                'PAPAL_ENCYCLICAL',
                'APOSTOLIC_EXHORTATION',
                'CANON_LAW',
                'LITURGICAL_BOOK',
                'DIOCESAN_DOCUMENT',
                'HISTORICAL_DOCUMENT',
                'APPROVED_PUBLICATION'
            ])->default('CATECHISM');
            $table->string('edition')->nullable();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->string('official_url')->nullable();
            $table->text('copyright_notes')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->timestamps();
        });

        // 2. Structured Canonical References
        Schema::create('catholic_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('catholic_sources')->cascadeOnDelete();
            $table->string('citation_label'); // e.g. "CCC 1213-1216", "John 3:5", "YOUCAT 194", "Lumen Gentium #8"
            $table->string('book_or_volume')->nullable(); // "Gospel of John", "Book 2: The Sacraments"
            $table->unsignedSmallInteger('chapter_or_section')->nullable();
            $table->string('verse_or_paragraph_range')->nullable(); // "5", "1-15", "1213-1216"
            $table->text('excerpt_text')->nullable();
            $table->timestamps();

            $table->index(['source_id', 'citation_label']);
        });

        // 3. Multi-Type Study Resources
        Schema::create('study_resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('track_id')->constrained('taxonomy_tracks')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('taxonomy_categories')->nullOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('taxonomy_topics')->nullOnDelete();
            $table->foreignId('subtopic_id')->nullable()->constrained('taxonomy_subtopics')->nullOnDelete();
            $table->foreignId('concept_id')->nullable()->constrained('taxonomy_concepts')->nullOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('formation_levels')->nullOnDelete();
            $table->foreignId('age_band_id')->nullable()->constrained('age_bands')->nullOnDelete();

            $table->string('title');
            $table->string('slug');
            $table->string('subheading')->nullable();
            $table->enum('resource_type', [
                'STUDY_NOTE',
                'STUDY_GUIDE',
                'LESSON',
                'SUMMARY',
                'EXPLANATION',
                'DEFINITION',
                'CONCEPT',
                'BIBLE_STUDY',
                'TOPIC_OVERVIEW',
                'DEEP_DIVE',
                'FAQ',
                'COMMON_MISTAKE',
                'KEY_FACT',
                'TIMELINE',
                'BIOGRAPHY',
                'SAINT_PROFILE',
                'PRAYER_GUIDE',
                'REFLECTION',
                'DISCUSSION_GUIDE',
                'REVISION_NOTE',
                'EXAM_TIP',
                'MEMORY_TIP',
                'QUICK_REVIEW',
                'CASE_STUDY',
                'REAL_WORLD_APPLICATION',
                'COMPARISON',
                'TABLE',
                'CHRONOLOGY',
                'GLOSSARY',
                'FLASHCARD_SET',
                'QUIZ_EXPLANATION'
            ])->default('STUDY_NOTE');

            $table->unsignedSmallInteger('estimated_read_minutes')->default(3); // Micro-learning support: 1, 3, 5, 10, 20
            $table->unsignedTinyInteger('difficulty_rating')->default(2); // 1 to 6
            $table->string('language_code', 10)->default('en'); // "en", "bem", "nya", "ton", "loz"

            // Educational Structured Metadata
            $table->text('why_this_matters')->nullable();
            $table->json('learning_objectives')->nullable(); // ["Understand Baptism effects", ...]
            $table->text('key_idea')->nullable();
            $table->longText('content_body');
            $table->json('content_sections')->nullable(); // [{heading, body, quote, callout}]
            $table->json('key_terms')->nullable(); // [{term, definition}]
            $table->json('common_misconceptions')->nullable(); // [{misconception, correction}]
            $table->text('practical_application')->nullable();
            $table->json('reflection_questions')->nullable();
            $table->json('revision_points')->nullable();
            $table->json('keywords')->nullable();
            $table->json('prerequisite_resource_ids')->nullable();

            // Governance & Review Lifecycle
            $table->enum('status', [
                'DRAFT',
                'AI_GENERATED',
                'IMPORTED',
                'UNDER_REVIEW',
                'NEEDS_REVISION',
                'APPROVED',
                'PUBLISHED',
                'ARCHIVED',
                'REJECTED'
            ])->default('DRAFT');

            $table->enum('ownership_scope', [
                'DIOCESAN',
                'PARISH',
                'SYSTEM',
                'PARTNER',
                'IMPORTED',
                'AI_ASSISTED'
            ])->default('DIOCESAN');

            $table->enum('visibility', [
                'PUBLIC',
                'DIOCESAN',
                'DEANERY',
                'PARISH',
                'PRIVATE'
            ])->default('DIOCESAN');

            $table->foreignUuid('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('theological_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('current_version')->default(1);
            $table->timestamps();

            $table->index(['track_id', 'status', 'resource_type']);
            $table->index(['topic_id', 'status', 'level_id']);
            $table->index(['concept_id', 'status']);
            $table->index(['language_code', 'status']);
        });

        // 4. Study Resource Immutable Version History
        Schema::create('study_resource_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('study_resource_id')->constrained('study_resources')->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('title');
            $table->string('subheading')->nullable();
            $table->longText('content_body');
            $table->json('content_sections')->nullable();
            $table->json('key_terms')->nullable();
            $table->json('learning_objectives')->nullable();
            $table->string('changelog_notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['study_resource_id', 'version_number']);
        });

        // 5. Saint Knowledge Bank (African & Universal Church)
        Schema::create('saint_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "St. Josephine Bakhita", "St. Charles Lwanga", "St. Theresa of the Child Jesus"
            $table->string('slug')->unique();
            $table->string('title_designation')->nullable(); // "Virgin and Martyr", "Doctor of the Church"
            $table->string('feast_day_month_day', 10); // "02-08" for Feb 8, "10-01" for Oct 1
            $table->string('birth_year')->nullable();
            $table->string('death_year')->nullable();
            $table->string('country_region')->nullable(); // "Sudan", "Uganda", "Zambia", "Italy"
            $table->boolean('is_african_heritage')->default(false);
            $table->json('patronages')->nullable(); // ["Sudan", "Human Trafficking Victims"]
            $table->longText('biography');
            $table->json('virtues_exemplified')->nullable(); // ["Forgiveness", "Courage"]
            $table->json('key_teachings_quotes')->nullable();
            $table->text('patronage_prayer')->nullable();
            $table->string('icon_or_image_url')->nullable();
            $table->timestamps();

            $table->index(['feast_day_month_day', 'is_african_heritage']);
        });

        // 6. Content Reference Link Table
        Schema::create('content_references', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('study_resource_id')->constrained('study_resources')->cascadeOnDelete();
            $table->foreignId('catholic_reference_id')->constrained('catholic_references')->cascadeOnDelete();
            $table->string('relevance_note')->nullable();
            $table->timestamps();

            $table->unique(['study_resource_id', 'catholic_reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_references');
        Schema::dropIfExists('saint_profiles');
        Schema::dropIfExists('study_resource_versions');
        Schema::dropIfExists('study_resources');
        Schema::dropIfExists('catholic_references');
        Schema::dropIfExists('catholic_sources');
    }
};
