<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBankItem extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'track_id',
        'category_id',
        'topic_id',
        'subtopic_id',
        'concept_id',
        'level_id',
        'age_band_id',
        'bloom_id',
        'question_type',
        'question_text',
        'explanation',
        'teaching_point',
        'correct_answer_payload',
        'reference_citation',
        'image_url',
        'language_code',
        'editorial_difficulty',
        'empirical_difficulty',
        'discrimination_index',
        'times_served',
        'times_answered',
        'times_correct',
        'times_incorrect',
        'avg_response_time_seconds',
        'health_score',
        'duplicate_similarity_hash',
        'parent_question_id',
        'duplicate_cluster_id',
        'status',
        'is_competition_eligible',
        'is_practice_eligible',
        'author_id',
        'reviewer_id',
        'theological_reviewer_id',
        'reviewed_at',
        'published_at',
        'current_version',
    ];

    protected $casts = [
        'is_competition_eligible' => 'boolean',
        'is_practice_eligible' => 'boolean',
        'empirical_difficulty' => 'float',
        'discrimination_index' => 'float',
        'avg_response_time_seconds' => 'float',
        'times_served' => 'integer',
        'times_answered' => 'integer',
        'times_correct' => 'integer',
        'times_incorrect' => 'integer',
        'health_score' => 'integer',
        'current_version' => 'integer',
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function track()
    {
        return $this->belongsTo(TaxonomyTrack::class, 'track_id');
    }

    public function category()
    {
        return $this->belongsTo(TaxonomyCategory::class, 'category_id');
    }

    public function topic()
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }

    public function subtopic()
    {
        return $this->belongsTo(TaxonomySubtopic::class, 'subtopic_id');
    }

    public function concept()
    {
        return $this->belongsTo(TaxonomyConcept::class, 'concept_id');
    }

    public function level()
    {
        return $this->belongsTo(FormationLevel::class, 'level_id');
    }

    public function ageBand()
    {
        return $this->belongsTo(AgeBand::class, 'age_band_id');
    }

    public function bloomTaxonomy()
    {
        return $this->belongsTo(BloomTaxonomy::class, 'bloom_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_bank_item_id')->orderBy('sort_order');
    }

    public function versions()
    {
        return $this->hasMany(QuestionVersion::class, 'question_bank_item_id')->orderByDesc('version_number');
    }

    public function pools()
    {
        return $this->belongsToMany(QuestionPool::class, 'question_pool_items', 'question_bank_item_id', 'question_pool_id');
    }

    public function parentQuestion()
    {
        return $this->belongsTo(QuestionBankItem::class, 'parent_question_id');
    }

    public function variants()
    {
        return $this->hasMany(QuestionBankItem::class, 'parent_question_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function theologicalReviewer()
    {
        return $this->belongsTo(User::class, 'theological_reviewer_id');
    }
}
