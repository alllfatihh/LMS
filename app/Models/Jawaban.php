<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jawaban extends Model
{
    protected $table = 'jawaban';

    protected $fillable = ['sesi_ujian_id', 'soal_id', 'generated_variables', 'student_answer', 'is_correct'];

    protected function casts(): array
    {
        return [
            'generated_variables' => 'array',
            'is_correct' => 'boolean',
        ];
    }

    public function sesiUjian(): BelongsTo
    {
        return $this->belongsTo(SesiUjian::class, 'sesi_ujian_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }
}
