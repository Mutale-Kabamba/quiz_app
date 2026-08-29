<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyNote extends Model
{
    use HasUuids;

    protected $fillable = [
        'category_id',
        'title',
        'subheading',
        'content_body',
        'reference_code',
        'downloadable_pdf_url',
        'estimated_read_minutes',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
