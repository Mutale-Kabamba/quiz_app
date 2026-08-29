<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormationLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_number',
        'name',
        'code',
        'description',
        'min_xp_required',
    ];

    public function studyResources()
    {
        return $this->hasMany(StudyResource::class, 'level_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionBankItem::class, 'level_id');
    }
}
