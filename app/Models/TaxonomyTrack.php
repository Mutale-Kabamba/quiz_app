<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxonomyTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'name',
        'slug',
        'code',
        'description',
        'icon',
        'color_theme',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function domain()
    {
        return $this->belongsTo(TaxonomyDomain::class, 'domain_id');
    }

    public function categories()
    {
        return $this->hasMany(TaxonomyCategory::class, 'track_id')->orderBy('display_order');
    }

    public function studyResources()
    {
        return $this->hasMany(StudyResource::class, 'track_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionBankItem::class, 'track_id');
    }
}
