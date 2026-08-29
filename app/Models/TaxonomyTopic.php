<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxonomyTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'description',
        'icon',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(TaxonomyCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(TaxonomySubcategory::class, 'subcategory_id');
    }

    public function subtopics()
    {
        return $this->hasMany(TaxonomySubtopic::class, 'topic_id')->orderBy('display_order');
    }

    public function concepts()
    {
        return $this->hasMany(TaxonomyConcept::class, 'topic_id')->orderBy('display_order');
    }

    public function studyResources()
    {
        return $this->hasMany(StudyResource::class, 'topic_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionBankItem::class, 'topic_id');
    }

    public function userMasteries()
    {
        return $this->hasMany(UserTopicMastery::class, 'topic_id');
    }
}
