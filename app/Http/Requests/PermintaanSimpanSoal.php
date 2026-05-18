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
            'question_template' => ['required', 'string'],
            'variable_rules' => ['required', 'array'],
            'variable_rules.*.min' => ['required', 'numeric'],
            'variable_rules.*.max' => ['required', 'numeric', 'gte:variable_rules.*.min'],
            'answer_formula' => ['required', 'string'],
        ];
    }
}
