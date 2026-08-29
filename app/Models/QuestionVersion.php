<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_item_id',
        'version_number',
        'question_text',
        'options_snapshot',
        'correct_answer_snapshot',
        'explanation_snapshot',
        'reference_citation_snapshot',
        'changelog_notes',
        'created_by',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'options_snapshot' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(QuestionBankItem::class, 'question_bank_item_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
