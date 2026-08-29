<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloomTaxonomy extends Model
{
    use HasFactory;

    protected $table = 'bloom_taxonomies';

    protected $fillable = [
        'code',
        'name',
        'cognitive_order',
        'description',
    ];

    public function questions()
    {
        return $this->hasMany(QuestionBankItem::class, 'bloom_id');
    }
}
