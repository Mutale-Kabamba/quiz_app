<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RallyPreparation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'rally_date',
        'description',
        'target_questions_count',
        'domain_weights',
        'is_active',
    ];

    protected $casts = [
        'rally_date' => 'date',
        'target_questions_count' => 'integer',
        'domain_weights' => 'array',
        'is_active' => 'boolean',
    ];

    public function userReadiness(): HasMany
    {
        return $this->hasMany(UserRallyReadiness::class, 'rally_id');
    }
}
