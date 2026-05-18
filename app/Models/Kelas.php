<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['name', 'description', 'tahun', 'class_code', 'guru_id'];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
        ];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function daftarSoal()
    {
        return $this->hasManyThrough(Soal::class, Bab::class, 'kelas_id', 'bab_id');
    }

    public function bab(): HasMany
    {
        return $this->hasMany(Bab::class, 'kelas_id');
    }

    public function daftarSesiUjian()
    {
        return $this->hasManyThrough(SesiUjian::class, Bab::class, 'kelas_id', 'bab_id');
    }

    public function siswa()
    {
        return $this->belongsToMany(User::class, 'kelas_user', 'kelas_id', 'user_id');
    }

    public function modulMateri(): HasMany
    {
        return $this->hasMany(Materi::class, 'kelas_id')->orderBy('order');
    }
}
