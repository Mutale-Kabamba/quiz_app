<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentReviewLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'reviewer_id',
        'action',
        'theological_accuracy_rating',
        'clarity_rating',
        'reviewer_comments',
        'old_status',
        'new_status',
    ];

    protected $casts = [
        'theological_accuracy_rating' => 'integer',
        'clarity_rating' => 'integer',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewable()
    {
        return $this->morphTo();
    }
}
