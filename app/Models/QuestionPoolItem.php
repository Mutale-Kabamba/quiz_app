<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPoolItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_pool_id',
        'question_bank_item_id',
    ];

    public function pool()
    {
        return $this->belongsTo(QuestionPool::class, 'question_pool_id');
    }

    public function question()
    {
        return $this->belongsTo(QuestionBankItem::class, 'question_bank_item_id');
    }
}
