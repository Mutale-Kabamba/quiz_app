<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'track_id',
        'level_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function track()
    {
        return $this->belongsTo(TaxonomyTrack::class, 'track_id');
    }

    public function level()
    {
        return $this->belongsTo(FormationLevel::class, 'level_id');
    }

    public function questions()
    {
        return $this->belongsToMany(QuestionBankItem::class, 'question_pool_items', 'question_pool_id', 'question_bank_item_id');
    }
}
