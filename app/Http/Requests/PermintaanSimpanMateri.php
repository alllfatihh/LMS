<?php

namespace App\Http\Requests;

use App\Models\Bab;
use App\Models\Materi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermintaanSimpanMateri extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'guru') {
            return false;
        }

        $bab = $this->route('bab');
        if ($bab instanceof Bab) {
            $bab->loadMissing('kelas');

            return (int) $bab->kelas->guru_id === (int) $user->id;
        }

        $materi = $this->route('materi');
        if ($materi instanceof Materi) {
            $materi->loadMissing('kelas');

            return (int) $materi->kelas->guru_id === (int) $user->id;
        }

        return false;
    }

    public function rules(): array
    {
        $tipe = strtolower(trim((string)$this->input('tipe')));

        return [
            'judul'       => ['required', 'string', 'min:3', 'max:255'],
            'ringkasan'   => ['nullable', 'string', 'max:1000'],
            'tipe'        => ['required', Rule::in(['teks', 'video', 'pdf'])],
            'order'       => ['nullable', 'integer', 'min:0', 'max:9999'],
            'konten_teks' => [Rule::requiredIf($tipe === 'teks'), 'nullable', 'string'],
            'link_video'  => [
                Rule::requiredIf($tipe === 'video'),
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) use ($tipe): void {
                    if ($tipe !== 'video' || !filled($value)) {
                        return;
                    }
                    if (! \App\Support\Youtube::extractVideoId((string) $value)) {
                        $fail('Tautan YouTube tidak dikenali. Pastikan URL benar.');
                    }
                },
            ],
            'berkas_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:12288'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $v): void {
            if ($this->route('bab') && $this->input('tipe') === 'pdf' && ! $this->hasFile('berkas_pdf')) {
                $v->errors()->add('berkas_pdf', 'Unggah berkas PDF.');
            }

            $materi = $this->route('materi');
            if ($materi instanceof Materi && $this->input('tipe') === 'pdf') {
                if (! $this->hasFile('berkas_pdf') && ! filled($materi->file_path)) {
                    $v->errors()->add('berkas_pdf', 'Unggah berkas PDF.');
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul materi',
            'ringkasan' => 'ringkasan',
            'tipe' => 'format materi',
            'konten_teks' => 'isi teks',
            'link_video' => 'tautan YouTube',
            'berkas_pdf' => 'berkas PDF',
        ];
    }
}
