<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatholicReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'citation_label',
        'book_or_volume',
        'chapter_or_section',
        'verse_or_paragraph_range',
        'excerpt_text',
    ];

    protected $casts = [
        'chapter_or_section' => 'integer',
    ];

    public function source()
    {
        return $this->belongsTo(CatholicSource::class, 'source_id');
    }

    public function studyResources()
    {
        return $this->belongsToMany(StudyResource::class, 'content_references', 'catholic_reference_id', 'study_resource_id')
            ->withPivot('relevance_note')
            ->withTimestamps();
    }
}
