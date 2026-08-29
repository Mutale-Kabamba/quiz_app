<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentPack extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_name',
        'pack_code',
        'description',
        'version',
        'checksum_hash',
        'size_bytes',
        'included_resource_ids',
        'included_question_ids',
        'is_published',
    ];

    protected $casts = [
        'version' => 'integer',
        'size_bytes' => 'integer',
        'included_resource_ids' => 'array',
        'included_question_ids' => 'array',
        'is_published' => 'boolean',
    ];
}
