<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiUjian extends Model
{
    protected $table = 'sesi_ujian';

    protected $fillable = ['user_id', 'bab_id', 'started_at', 'finished_at'];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bab(): BelongsTo
    {
        return $this->belongsTo(Bab::class, 'bab_id');
    }

    public function daftarJawaban(): HasMany
    {
        return $this->hasMany(Jawaban::class, 'sesi_ujian_id');
    }
}
