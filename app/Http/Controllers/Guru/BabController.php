<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BabController extends Controller
{
    public function buat(Kelas $kelas)
    {
        if ($kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        return view('guru.bab.buat', compact('kelas'));
    }

    public function simpan(Request $permintaan, Kelas $kelas)
    {
        if ($kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $permintaan->validate(['title' => 'required|string|max:255', 'order' => 'required|integer']);

        Bab::create([
            'kelas_id' => $kelas->id,
            'title' => $permintaan->title,
            'order' => $permintaan->order,
        ]);

        return redirect()->route('guru.kelas.tampil', $kelas->id)->with('success', 'Bab baru berhasil ditambahkan.');
    }

    public function tampil(Bab $bab)
    {
        if ($bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $materiTerhubungBab = Schema::hasTable('materi') && Schema::hasColumn('materi', 'bab_id');

        if ($materiTerhubungBab) {
            $bab->load(['teksBab', 'daftarSoal', 'materi']);
            $daftarMateriBab = $bab->materi;
        } else {
            $bab->load(['teksBab', 'daftarSoal']);
            $daftarMateriBab = collect();
        }

        return view('guru.bab.tampil', compact('bab', 'daftarMateriBab', 'materiTerhubungBab'));
    }

    public function edit(Bab $bab)
    {
        if ($bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        return view('guru.bab.edit', compact('bab'));
    }

    public function perbarui(Request $permintaan, Bab $bab)
    {
        if ($bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $permintaan->validate(['title' => 'required|string|max:255', 'order' => 'required|integer']);
        $bab->update(['title' => $permintaan->title, 'order' => $permintaan->order]);

        return redirect()->route('guru.bab.tampil', $bab->id)->with('success', 'Bab berhasil diperbarui.');
    }

    public function hapus(Bab $bab)
    {
        if ($bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $idKelas = $bab->kelas_id;
        $bab->delete();

        return redirect()->route('guru.kelas.tampil', $idKelas)->with('success', 'Bab sudah dihapus.');
    }
}
