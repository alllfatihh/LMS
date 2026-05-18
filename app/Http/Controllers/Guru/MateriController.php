<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermintaanSimpanMateri;
use App\Models\Bab;
use App\Models\Kelas;
use App\Models\Materi;
use App\Support\Youtube;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MateriController extends Controller
{
    public function buat(Bab $bab): View|RedirectResponse
    {
        abort_unless((int) $bab->kelas->guru_id === (int) auth()->id(), 403);

        if (! $this->materiPunyaKolomBabId()) {
            return $this->redirectMigrasi($bab->kelas_id);
        }

        $kelas = $bab->kelas;
        $urutanBerikutnya = (int) $bab->materi()->max('order') + 1;

        return view('guru.materi.buat', compact('bab', 'kelas', 'urutanBerikutnya'));
    }

    public function simpan(PermintaanSimpanMateri $permintaan, Bab $bab): RedirectResponse
    {
        if (! $this->materiPunyaKolomBabId()) {
            return $this->redirectMigrasi($bab->kelas_id);
        }

        $kelas = $bab->kelas;
        $slug = $this->slugUnikUntukKelas($kelas, $permintaan->string('judul'));
        $payload = $this->payloadDariPermintaan($permintaan, $kelas, $bab, $slug, null);

        Materi::create($payload);

        return redirect()
            ->route('guru.bab.tampil', $bab)
            ->with('success', 'Materi berhasil ditambahkan pada bab ini.');
    }

    public function edit(Materi $materi): View|RedirectResponse
    {
        $this->authorize('kelola', $materi);

        if (! $this->materiPunyaKolomBabId()) {
            return $this->redirectMigrasi($materi->kelas_id);
        }

        $materi->load(['kelas', 'bab']);
        $kelas = $materi->kelas;
        $bab = $materi->bab;

        return view('guru.materi.edit', compact('materi', 'kelas', 'bab'));
    }

    public function perbarui(PermintaanSimpanMateri $permintaan, Materi $materi): RedirectResponse
    {
        $this->authorize('kelola', $materi);

        if (! $this->materiPunyaKolomBabId()) {
            return $this->redirectMigrasi($materi->kelas_id);
        }

        $materi->load(['kelas', 'bab']);
        $kelas = $materi->kelas;
        $slug = $this->slugUnikUntukKelas($kelas, $permintaan->string('judul'), $materi->id);
        $payload = $this->payloadDariPermintaan($permintaan, $kelas, $materi->bab, $slug, $materi);
        
        $materi->update($payload);

        return redirect()
            ->route('siswa.materi.tampil', $materi->fresh()->slug)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function hapus(Materi $materi): RedirectResponse
    {
        $this->authorize('kelola', $materi);

        if (! $this->materiPunyaKolomBabId()) {
            return $this->redirectMigrasi($materi->kelas_id);
        }

        $kelas = $materi->kelas;
        $materi->loadMissing('bab');
        $bab = $materi->bab;

        if ($materi->tipe === Materi::TIPE_PDF && filled($materi->file_path)) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        if ($bab) {
            return redirect()
                ->route('guru.bab.tampil', $bab)
                ->with('success', 'Materi telah dihapus.');
        }

        return redirect()
            ->route('guru.kelas.tampil', $kelas)
            ->with('success', 'Materi telah dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadDariPermintaan(PermintaanSimpanMateri $permintaan, Kelas $kelas, ?Bab $bab, string $slug, ?Materi $existing): array
    {
        // Normalisasi tipe agar pasti cocok dengan konstanta
        $tipe = strtolower(trim($permintaan->string('tipe')));

        $orderBaru = $existing
            ? (int) ($permintaan->input('order') ?: $existing->order)
            : (int) ($permintaan->input('order') ?: (($bab ? (int) $bab->materi()->max('order') : 0) + 1));

        $data = [
            'kelas_id'  => $kelas->id,
            'bab_id'    => $existing?->bab_id ?? $bab?->id,
            'judul'     => $permintaan->string('judul'),
            'slug'      => $slug,
            'ringkasan' => $permintaan->filled('ringkasan') ? $permintaan->string('ringkasan') : null,
            'tipe'      => $tipe,
            'order'     => $orderBaru,
        ];

        // Reset semua field konten dulu agar tidak ada data sampah dari tipe sebelumnya
        $data['konten_teks'] = null;
        $data['link_video']  = null;
        $data['file_path']   = null;

        if ($tipe === 'teks') {
            $data['konten_teks'] = $permintaan->input('konten_teks');
        } elseif ($tipe === 'video') {
            $urlRaw = trim($permintaan->string('link_video'));
            $vId = \App\Support\Youtube::extractVideoId($urlRaw);
            
            // Simpan link video. Jika berhasil di-extract, simpan versi clean. Jika gagal, simpan aslinya.
            $data['link_video'] = $vId ? 'https://www.youtube.com/watch?v=' . $vId : $urlRaw;
        } elseif ($tipe === 'pdf') {
            $pathLama = $existing?->file_path;
            if ($permintaan->hasFile('berkas_pdf')) {
                $unggahan = $permintaan->file('berkas_pdf');
                if ($pathLama) {
                    Storage::disk('public')->delete($pathLama);
                }
                $namaBerkas = \Illuminate\Support\Str::uuid()->toString() . '.pdf';
                $data['file_path'] = $unggahan->storeAs('materi/' . $kelas->id, $namaBerkas, 'public');
            } elseif ($existing && $existing->tipe === 'pdf' && filled($pathLama)) {
                $data['file_path'] = $pathLama;
            }
        }

        return $data;
    }

    private function slugUnikUntukKelas(Kelas $kelas, string $judul, ?int $kecualiId = null): string
    {
        $base = Str::slug($judul);
        if ($base === '') {
            $base = 'materi';
        }

        $slug = $base;
        $i = 2;
        while (
            Materi::query()
                ->where('slug', $slug)
                ->when($kecualiId, fn ($q) => $q->where('id', '!=', $kecualiId))
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function materiPunyaKolomBabId(): bool
    {
        return Schema::hasTable('materi') && Schema::hasColumn('materi', 'bab_id');
    }

    private function redirectMigrasi(int $kelasId): RedirectResponse
    {
        return redirect()
            ->route('guru.kelas.tampil', $kelasId)
            ->with('error', 'Database perlu diperbarui: jalankan `php artisan migrate` di server (menambahkan kolom bab_id pada tabel materi).');
    }
}
