<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_item_id',
        'option_key',
        'option_text',
        'is_correct',
        'explanation_why_incorrect',
        'sort_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(QuestionBankItem::class, 'question_bank_item_id');
    }
}
