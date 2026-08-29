<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxonomySubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'code',
        'description',
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

    public function topics()
    {
        return $this->hasMany(TaxonomyTopic::class, 'subcategory_id')->orderBy('display_order');
    }
}
