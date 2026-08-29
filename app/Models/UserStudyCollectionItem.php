<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStudyCollectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',
        'item_type',
        'item_id',
        'custom_notes',
    ];

    public function collection()
    {
        return $this->belongsTo(UserStudyCollection::class, 'collection_id');
    }
}
