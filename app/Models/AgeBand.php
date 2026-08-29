<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeBand extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'min_age',
        'max_age',
        'description',
    ];

    public function studyResources()
    {
        return $this->hasMany(StudyResource::class, 'age_band_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionBankItem::class, 'age_band_id');
    }
}
