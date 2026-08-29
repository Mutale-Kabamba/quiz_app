<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStudyCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'collection_name',
        'color_tag',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(UserStudyCollectionItem::class, 'collection_id');
    }
}
