<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermintaanSimpanSoal extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'guru';
    }

    public function rules(): array
    {
        return [
            'question_template'      => ['required', 'string'],
            'variables'              => ['required', 'array', 'min:1'],
            'variables.*.name'       => ['required', 'string', 'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/'],
            'variables.*.min'        => ['nullable', 'numeric'],
            'variables.*.max'        => ['nullable', 'numeric'],
            'variables.*.type'       => ['nullable', 'string', 'in:integer,float,set,derived,number'],
            'variables.*.formula'    => ['nullable', 'string'],
            'variables.*.kelipatan'  => ['nullable', 'numeric', 'min:1'],
            'variables.*.decimals'   => ['nullable', 'numeric', 'min:0', 'max:5'],
            'variables.*.count'      => ['nullable', 'numeric', 'min:1'],
            'answer_formula'         => ['required', 'string'],
            'graph_formula'          => ['nullable', 'string'],
            'jawaban_format'         => 'required|string|in:bulat,desimal,angka,pecahan,koordinat,himpunan,aljabar,akar',
            'bobot'                  => 'required|integer|min:1',
            'pembahasan_template'    => 'nullable|string',
            'image'                  => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'variables.required'       => 'Minimal harus ada satu variabel.',
            'variables.min'            => 'Minimal harus ada satu variabel.',
            'variables.*.name.required' => 'Nama variabel wajib diisi.',
            'variables.*.name.regex'    => 'Nama variabel hanya boleh huruf, angka, dan underscore, serta tidak boleh diawali angka.',
            'variables.*.min.required'  => 'Nilai minimum wajib diisi.',
            'variables.*.max.required'  => 'Nilai maksimum wajib diisi.',
            'variables.*.max.gte'       => 'Nilai maksimum harus lebih besar atau sama dengan nilai minimum.',
        ];
    }
}
