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
            'question_template'    => ['required', 'string'],
            'variables'            => ['required', 'array', 'min:1'],
            'variables.*.name'     => ['required', 'string', 'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/'],
            'variables.*.min'      => ['required', 'numeric'],
            'variables.*.max'      => ['required', 'numeric', 'gte:variables.*.min'],
            'answer_formula'       => ['required', 'string'],
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

