<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_resource_id',
        'catholic_reference_id',
        'relevance_note',
    ];

    public function studyResource()
    {
        return $this->belongsTo(StudyResource::class, 'study_resource_id');
    }

    public function reference()
    {
        return $this->belongsTo(CatholicReference::class, 'catholic_reference_id');
    }
}
