<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatholicSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'short_code',
        'publisher_authority',
        'document_type',
        'edition',
        'publication_year',
        'official_url',
        'copyright_notes',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'publication_year' => 'integer',
    ];

    public function references()
    {
        return $this->hasMany(CatholicReference::class, 'source_id');
    }
}
