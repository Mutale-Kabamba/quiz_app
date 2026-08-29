<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Formation Dimensions & Lookup Tables
        Schema::create('formation_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('level_number')->unique(); // 1 to 6
            $table->string('name'); // e.g. "Foundation", "Beginner", "Intermediate", "Advanced", "Expert", "Competition"
            $table->string('code')->unique(); // "foundation", "beginner", "intermediate", "advanced", "expert", "competition"
            $table->text('description')->nullable();
            $table->unsignedInteger('min_xp_required')->default(0);
            $table->timestamps();
        });

        Schema::create('age_bands', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // "children", "early_teens", "teens", "youth", "young_adults", "adult_formation"
            $table->string('name');
            $table->unsignedTinyInteger('min_age')->default(0);
            $table->unsignedTinyInteger('max_age')->default(99);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('bloom_taxonomies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // "remember", "understand", "apply", "analyze", "evaluate", "create"
            $table->string('name');
            $table->unsignedTinyInteger('cognitive_order')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('difficulty_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // "very_easy", "easy", "medium", "hard", "very_hard", "expert"
            $table->string('name');
            $table->unsignedTinyInteger('weight')->default(1); // 1 to 6
            $table->float('target_accuracy_min')->default(0.0);
            $table->float('target_accuracy_max')->default(1.0);
            $table->timestamps();
        });

        // 2. Hierarchical Taxonomy System
        Schema::create('taxonomy_domains', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Catholic Formation"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('taxonomy_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->nullable()->constrained('taxonomy_domains')->nullOnDelete();
            $table->string('name'); // e.g. "Holy Scripture", "YOUCAT", "DOCAT", "CCC", "African Church History"
            $table->string('slug')->unique();
            $table->string('code')->unique(); // e.g. "SCRIPTURE", "YOUCAT", "CCC", "AFRICA_CHURCH"
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color_theme', 30)->nullable();
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['domain_id', 'is_active', 'display_order']);
        });

        Schema::create('taxonomy_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained('taxonomy_tracks')->cascadeOnDelete();
            $table->string('name'); // e.g. "Old Testament", "New Testament", "Sacraments of Initiation"
            $table->string('slug');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['track_id', 'slug']);
            $table->index(['track_id', 'is_active', 'display_order']);
        });

        Schema::create('taxonomy_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('taxonomy_categories')->cascadeOnDelete();
            $table->string('name'); // e.g. "Pentateuch", "Gospels", "Pauline Epistles"
            $table->string('slug');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category_id', 'slug']);
            $table->index(['category_id', 'is_active']);
        });

        Schema::create('taxonomy_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->nullable()->constrained('taxonomy_subcategories')->nullOnDelete();
            $table->foreignId('category_id')->constrained('taxonomy_categories')->cascadeOnDelete();
            $table->string('name'); // e.g. "Exodus", "Gospel of Mark", "Sacrament of Baptism"
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category_id', 'slug']);
            $table->index(['category_id', 'subcategory_id', 'is_active']);
        });

        Schema::create('taxonomy_subtopics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('taxonomy_topics')->cascadeOnDelete();
            $table->string('name'); // e.g. "Moses and the Call of God", "The Institution of the Eucharist"
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['topic_id', 'slug']);
            $table->index(['topic_id', 'is_active']);
        });

        Schema::create('taxonomy_concepts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('taxonomy_topics')->cascadeOnDelete();
            $table->foreignId('subtopic_id')->nullable()->constrained('taxonomy_subtopics')->nullOnDelete();
            $table->string('name'); // e.g. "The Burning Bush", "Transubstantiation", "Original Sin", "Marks of the Church"
            $table->string('slug');
            $table->text('summary_definition')->nullable();
            $table->json('canonical_terms')->nullable(); // Synonyms & keywords
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['topic_id', 'slug']);
            $table->index(['topic_id', 'subtopic_id', 'is_active']);
        });

        // 3. Knowledge Graph - Concept Relationships
        Schema::create('concept_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_concept_id')->constrained('taxonomy_concepts')->cascadeOnDelete();
            $table->foreignId('target_concept_id')->constrained('taxonomy_concepts')->cascadeOnDelete();
            $table->enum('relationship_type', [
                'RELATED_TO',
                'PREREQUISITE_OF',
                'PART_OF',
                'OPPOSITE_OF',
                'EXAMPLE_OF',
                'EXPLAINS',
                'BUILDS_ON',
                'COMMONLY_CONFUSED_WITH'
            ])->default('RELATED_TO');
            $table->text('pedagogical_note')->nullable();
            $table->timestamps();

            $table->unique(['source_concept_id', 'target_concept_id', 'relationship_type'], 'concept_rel_unique');
            $table->index(['source_concept_id', 'relationship_type']);
            $table->index(['target_concept_id', 'relationship_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concept_relationships');
        Schema::dropIfExists('taxonomy_concepts');
        Schema::dropIfExists('taxonomy_subtopics');
        Schema::dropIfExists('taxonomy_topics');
        Schema::dropIfExists('taxonomy_subcategories');
        Schema::dropIfExists('taxonomy_categories');
        Schema::dropIfExists('taxonomy_tracks');
        Schema::dropIfExists('taxonomy_domains');
        Schema::dropIfExists('difficulty_tiers');
        Schema::dropIfExists('bloom_taxonomies');
        Schema::dropIfExists('age_bands');
        Schema::dropIfExists('formation_levels');
    }
};
