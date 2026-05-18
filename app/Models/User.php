<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kelasYangDiajar(): HasMany
    {
        return $this->hasMany(Kelas::class, 'guru_id');
    }

    public function daftarSesiUjian(): HasMany
    {
        return $this->hasMany(SesiUjian::class, 'user_id');
    }

    public function kelasYangDiikuti()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_user', 'user_id', 'kelas_id');
    }

    public function babYangSudahDibaca()
    {
        return $this->belongsToMany(Bab::class, 'kemajuan_bab')->withPivot('read_at')->withTimestamps();
    }

    public function sudahMembacaBab(int $babId): bool
    {
        return $this->babYangSudahDibaca()->where('bab_id', $babId)->exists();
    }

    public function materiYangSudahDibaca()
    {
        return $this->belongsToMany(Materi::class, 'kemajuan_materi')->withPivot('read_at')->withTimestamps();
    }

    public function sudahMembacaMateri(int $materiId): bool
    {
        return $this->materiYangSudahDibaca()->where('materi_id', $materiId)->exists();
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }
}
