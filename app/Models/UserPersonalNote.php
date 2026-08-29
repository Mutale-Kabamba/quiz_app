<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPersonalNote extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'title',
        'note_body',
        'study_resource_id',
        'topic_id',
        'scripture_reference_tag',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function studyResource()
    {
        return $this->belongsTo(StudyResource::class, 'study_resource_id');
    }

    public function topic()
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }
}
