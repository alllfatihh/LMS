<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Soal extends Model
{
    protected $table = 'soal';

    protected $fillable = ['bab_id', 'question_template', 'variable_rules', 'answer_formula'];

    protected function casts(): array
    {
        return [
            'variable_rules' => 'array',
        ];
    }

    public function bab(): BelongsTo
    {
        return $this->belongsTo(Bab::class, 'bab_id');
    }

    public function daftarJawaban(): HasMany
    {
        return $this->hasMany(Jawaban::class, 'soal_id');
    }
}
