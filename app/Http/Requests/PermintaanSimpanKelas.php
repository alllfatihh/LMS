<?php

namespace App\Http\Requests;

use App\Models\Kelas;
use Illuminate\Foundation\Http\FormRequest;

class PermintaanSimpanKelas extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'guru') {
            return false;
        }

        $kelas = $this->route('kelas');
        if ($kelas instanceof Kelas) {
            return (int) $kelas->guru_id === (int) $user->id;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama kelas',
            'description' => 'deskripsi',
            'tahun' => 'tahun ajaran',
        ];
    }
}
