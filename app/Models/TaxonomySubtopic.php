<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxonomySubtopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'name',
        'slug',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function topic()
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }

    public function concepts()
    {
        return $this->hasMany(TaxonomyConcept::class, 'subtopic_id')->orderBy('display_order');
    }
}
