<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class GabungKelasController extends Controller
{
    public function index()
    {
        return view('siswa.kelas.gabung');
    }

    public function simpan(Request $permintaan)
    {
        $permintaan->validate([
            'class_code' => ['required', 'string', 'min:3', 'max:10'],
        ], [
            'class_code.required' => 'Isi kode kelas dulu, ya.',
            'class_code.min' => 'Kode kelas minimal 3 huruf/angka.',
            'class_code.max' => 'Kode kelas paling panjang 10 karakter.',
        ]);

        $kode = strtoupper($permintaan->class_code);

        $kelas = Kelas::where('class_code', $kode)->first();

        if (! $kelas) {
            return back()->with('error', 'Kode kelas tidak ketemu. Coba tanya guru kamu, ya!');
        }

        $pengguna = auth()->user();

        if ($pengguna->kelasYangDiikuti()->where('kelas.id', $kelas->id)->exists()) {
            return redirect()->route('siswa.ujian.index')->with('success', 'Kamu sudah di kelas ini.');
        }

        $pengguna->kelasYangDiikuti()->attach($kelas->id);

        return redirect()->route('siswa.ujian.index')->with('success', 'Berhasil gabung ke kelas '.$kelas->name.'!');
    }
    public function keluar(Kelas $kelas)
    {
        $pengguna = auth()->user();
        
        $pengguna->kelasYangDiikuti()->detach($kelas->id);
        
        return redirect()->route('beranda')->with('success', 'Kamu telah keluar dari kelas ' . $kelas->name . '.');
    }
}
