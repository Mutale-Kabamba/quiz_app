<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentGenerationJob extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'job_title',
        'track_id',
        'category_id',
        'topic_id',
        'level_id',
        'content_kind',
        'requested_quantity',
        'generated_count',
        'accepted_count',
        'duplicate_count',
        'failed_count',
        'status',
        'generation_parameters',
        'execution_log',
        'initiated_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
        'generated_count' => 'integer',
        'accepted_count' => 'integer',
        'duplicate_count' => 'integer',
        'failed_count' => 'integer',
        'generation_parameters' => 'array',
        'execution_log' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function track()
    {
        return $this->belongsTo(TaxonomyTrack::class, 'track_id');
    }

    public function category()
    {
        return $this->belongsTo(TaxonomyCategory::class, 'category_id');
    }

    public function topic()
    {
        return $this->belongsTo(TaxonomyTopic::class, 'topic_id');
    }

    public function level()
    {
        return $this->belongsTo(FormationLevel::class, 'level_id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
}
