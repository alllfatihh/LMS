<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bab extends Model
{
    protected $table = 'bab';

    protected $fillable = ['kelas_id', 'title', 'order'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function teksBab(): HasOne
    {
        return $this->hasOne(TeksBab::class, 'bab_id');
    }

    public function daftarSoal(): HasMany
    {
        return $this->hasMany(Soal::class, 'bab_id');
    }

    public function daftarSesiUjian(): HasMany
    {
        return $this->hasMany(SesiUjian::class, 'bab_id');
    }

    public function materi(): HasMany
    {
        return $this->hasMany(Materi::class, 'bab_id')->orderBy('order')->orderBy('id');
    }

    public function dibacaOlehSiswa()
    {
        return $this->belongsToMany(User::class, 'kemajuan_bab')->withPivot('read_at')->withTimestamps();
    }
}
