<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaintProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'title_designation',
        'feast_day_month_day',
        'birth_year',
        'death_year',
        'country_region',
        'is_african_heritage',
        'patronages',
        'biography',
        'virtues_exemplified',
        'key_teachings_quotes',
        'patronage_prayer',
        'icon_or_image_url',
    ];

    protected $casts = [
        'is_african_heritage' => 'boolean',
        'patronages' => 'array',
        'virtues_exemplified' => 'array',
        'key_teachings_quotes' => 'array',
    ];
}
