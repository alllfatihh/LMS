<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermintaanSimpanKelas;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $guruId = auth()->id();

        $punyaKelas = Kelas::where('guru_id', $guruId)->exists();

        $tahunTersedia = Kelas::query()
            ->where('guru_id', $guruId)
            ->whereNotNull('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $tahunInput = $request->input('tahun');
        $tahunFilter = null;
        if ($tahunInput !== null && $tahunInput !== '') {
            $t = filter_var($tahunInput, FILTER_VALIDATE_INT);
            if ($t !== false && $tahunTersedia->contains((int) $t)) {
                $tahunFilter = (int) $t;
            }
        }

        $daftarKelas = Kelas::query()
            ->where('guru_id', $guruId)
            ->withCount('siswa')
            ->when(trim((string) $request->input('search', '')) !== '', function ($query) use ($request) {
                $kata = trim((string) $request->input('search', ''));
                $query->where('name', 'like', '%'.$kata.'%');
            })
            ->when($tahunFilter !== null, function ($query) use ($tahunFilter) {
                $query->where('tahun', $tahunFilter);
            })
            ->orderByDesc('tahun')
            ->orderBy('name')
            ->get();

        return view('guru.kelas.index', compact('daftarKelas', 'punyaKelas', 'tahunTersedia', 'tahunFilter'));
    }

    public function buat()
    {
        return view('guru.kelas.buat');
    }

    public function simpan(PermintaanSimpanKelas $permintaan)
    {
        $kode = Str::upper(Str::random(6));

        while (Kelas::where('class_code', $kode)->exists()) {
            $kode = Str::upper(Str::random(6));
        }

        $kelas = Kelas::create([
            'name' => $permintaan->name,
            'description' => $permintaan->description,
            'tahun' => $permintaan->integer('tahun'),
            'class_code' => $kode,
            'guru_id' => auth()->id(),
        ]);

        return redirect()->route('guru.kelas.tampil', $kelas)
            ->with('success', 'Kelas berhasil dibuat. Bagikan kode '.$kode.' ke siswa.');
    }

    public function tampil(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        $daftarBab = $kelas->bab()->with('teksBab')->withCount('daftarSoal')->orderBy('order')->get();
        $babIds = $daftarBab->pluck('id')->toArray();
        $jumlahBab = count($babIds);

        $materiTerhubungBab = Schema::hasTable('materi') && Schema::hasColumn('materi', 'bab_id');
        $queryMateri = $kelas->modulMateri()->orderBy('order')->orderBy('id');
        if ($materiTerhubungBab) {
            $queryMateri->with('bab');
        }
        $daftarMateri = $queryMateri->get();

        $semuaSiswa = $kelas->siswa()->orderBy('name')->get();
        $siswaIds = $semuaSiswa->pluck('id')->toArray();

        // 1. Ambil data progress baca untuk SELURUH siswa sekaligus (Bulk Query)
        $semuaBabDibaca = DB::table('kemajuan_bab')
            ->whereIn('bab_id', $babIds)
            ->whereIn('user_id', $siswaIds)
            ->get()
            ->groupBy('user_id');

        // 2. Ambil data riwayat ujian untuk SELURUH siswa sekaligus (Bulk Query + Eager Loading)
        $semuaSesiSelesai = \App\Models\SesiUjian::whereIn('user_id', $siswaIds)
            ->whereIn('bab_id', $babIds)
            ->whereNotNull('finished_at')
            ->with(['bab', 'daftarJawaban.soal'])
            ->latest('finished_at')
            ->get()
            ->map(function ($sesi) {
                $semuaBobot = 0;
                $bobotBenar = 0;
                foreach ($sesi->daftarJawaban as $jwb) {
                    $bobot = $jwb->soal->bobot ?? 10;
                    $semuaBobot += $bobot;
                    if ($jwb->is_correct) {
                        $bobotBenar += $bobot;
                    }
                }
                $sesi->nilai_akhir = $semuaBobot > 0 ? round(($bobotBenar / $semuaBobot) * 100) : 0;
                
                $duration = null;
                if ($sesi->finished_at && $sesi->started_at) {
                    $secs = $sesi->started_at->diffInSeconds($sesi->finished_at);
                    $mins = floor($secs / 60);
                    $remSecs = $secs % 60;
                    $duration = "{$mins}m {$remSecs}s";
                }
                $sesi->waktu_pengerjaan = $duration ?? '-';
                
                return $sesi;
            })
            ->groupBy('user_id');

        // 3. Mapping data siswa tanpa query tambahan (In-Memory Processing)
        $daftarSiswa = $semuaSiswa->map(function ($siswa) use ($daftarBab, $jumlahBab, $semuaBabDibaca, $semuaSesiSelesai) {
            // Ekstrak bab_id dari collection di memory
            $babIdsDibaca = isset($semuaBabDibaca[$siswa->id]) 
                ? $semuaBabDibaca[$siswa->id]->pluck('bab_id')->toArray() 
                : [];

            $jumlahBabDibaca = count($babIdsDibaca);
            $siswa->persen_kemajuan = $jumlahBab > 0
                ? ($jumlahBabDibaca / $jumlahBab) * 100
                : 0;
            $siswa->jumlah_bab_dibaca = $jumlahBabDibaca;
            $siswa->total_bab_kelas = $jumlahBab;
            $siswa->tanggal_bergabung = $siswa->pivot->created_at;

            $sesiSelesai = $semuaSesiSelesai->get($siswa->id, collect());
            $siswa->riwayat_ujian = $sesiSelesai;

            // Pemetaan status setiap bab untuk siswa ini
            // Gunakan groupBy()->map->first() agar mendapatkan sesi TERBARU (karena koleksi sudah terurut latest)
            $sesiByBabId = $sesiSelesai->groupBy('bab_id')->map->first();
            $siswa->status_per_bab = $daftarBab->map(function ($b) use ($babIdsDibaca, $sesiByBabId) {
                $sesi = $sesiByBabId->get($b->id);
                $isDibaca = in_array($b->id, $babIdsDibaca);
                return (object) [
                    'bab_id' => $b->id,
                    'order' => $b->order,
                    'title' => $b->title,
                    'is_dibaca' => $isDibaca,
                    'sesi' => $sesi,
                    'sesi_id' => $sesi?->id,
                    'nilai_akhir' => $sesi?->nilai_akhir,
                ];
            });

            return $siswa;
        });

        return view('guru.kelas.tampil', compact('kelas', 'daftarBab', 'daftarMateri', 'daftarSiswa', 'materiTerhubungBab'));
    }

    public function edit(Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        return view('guru.kelas.edit', compact('kelas'));
    }

    public function perbarui(PermintaanSimpanKelas $permintaan, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $kelas->update([
            'name' => $permintaan->name,
            'description' => $permintaan->description,
            'tahun' => $permintaan->integer('tahun'),
        ]);

        return redirect()->route('guru.kelas.tampil', $kelas)
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function hapus(Kelas $kelas)
    {
        $this->authorize('delete', $kelas);

        try {
            DB::transaction(function () use ($kelas) {
                $kelas->siswa()->detach();
                $kelas->delete();
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('guru.kelas.tampil', $kelas)
                ->with('error', 'Kelas tidak dapat dihapus saat ini. Coba lagi atau hubungi admin.');
        }

        return redirect()->route('guru.kelas.index')
            ->with('success', 'Kelas beserta data terkait (bab, materi, tautan siswa) telah dihapus.');
    }

    public function keluarkanSiswa(Kelas $kelas, \App\Models\User $siswa)
    {
        $this->authorize('update', $kelas);

        $kelas->siswa()->detach($siswa->id);

        return redirect()->route('guru.kelas.tampil', $kelas)
            ->with('success', "Siswa {$siswa->name} telah dikeluarkan dari kelas.");
    }
}
