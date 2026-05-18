<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Materi extends Model
{
    /** Pemisah blok opsional di dalam konten teks (rich HTML) */
    public const PEMBATAS_BLOK = '===BLOK===';

    public const TIPE_TEKS = 'teks';

    public const TIPE_VIDEO = 'video';

    public const TIPE_PDF = 'pdf';

    protected $table = 'materi';

    protected $fillable = [
        'kelas_id',
        'bab_id',
        'judul',
        'slug',
        'ringkasan',
        'tipe',
        'konten_teks',
        'link_video',
        'file_path',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function bab(): BelongsTo
    {
        return $this->belongsTo(Bab::class, 'bab_id');
    }

    /**
     * @return list<string>
     */
    public function daftarBlokKonten(): array
    {
        $html = (string) ($this->konten_teks ?? '');
        if (trim($html) === '') {
            return [];
        }

        $fragmen = array_map('trim', explode(self::PEMBATAS_BLOK, $html));
        $fragmen = array_values(array_filter($fragmen, fn (string $b): bool => $b !== ''));

        return $fragmen !== [] ? $fragmen : [$html];
    }

    public function labelTipe(): string
    {
        return match (strtolower(trim((string) $this->tipe))) {
            self::TIPE_VIDEO => 'Video YouTube',
            self::TIPE_PDF => 'PDF',
            default => 'Teks',
        };
    }

    /** @deprecated gunakan labelTipe() */
    public function labelTipeMedia(): string
    {
        return $this->labelTipe();
    }

    public function urlPublikPdf(): ?string
    {
        if ($this->tipe !== self::TIPE_PDF || ! filled($this->file_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    public function pembaca()
    {
        return $this->belongsToMany(User::class, 'kemajuan_materi')->withPivot('read_at')->withTimestamps();
    }
}
