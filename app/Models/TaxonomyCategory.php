<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxonomyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'track_id',
        'name',
        'slug',
        'code',
        'description',
        'icon',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function track()
    {
        return $this->belongsTo(TaxonomyTrack::class, 'track_id');
    }

    public function subcategories()
    {
        return $this->hasMany(TaxonomySubcategory::class, 'category_id')->orderBy('display_order');
    }

    public function topics()
    {
        return $this->hasMany(TaxonomyTopic::class, 'category_id')->orderBy('display_order');
    }

    public function studyResources()
    {
        return $this->hasMany(StudyResource::class, 'category_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionBankItem::class, 'category_id');
    }
}
