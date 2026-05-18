<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use App\Models\TeksBab;
use Illuminate\Http\Request;

class TeksBabController extends Controller
{
    public function edit(Bab $bab)
    {
        if ($bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $teksBab = $bab->teksBab;

        return view('guru.teks.edit', compact('bab', 'teksBab'));
    }

    public function perbarui(Request $permintaan, Bab $bab)
    {
        if ($bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $permintaan->validate([
            'content' => ['nullable', 'string'],
        ]);

        TeksBab::updateOrCreate(
            ['bab_id' => $bab->id],
            ['content' => (string) $permintaan->input('content', '')]
        );

        return redirect()->route('guru.bab.tampil', $bab->id)->with('success', 'Ringkasan bab berhasil disimpan.');
    }
}
