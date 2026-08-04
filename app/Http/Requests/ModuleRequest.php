<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modules', 'title')->ignore($this->route('module')), // Pengecualian saat update jika perlu
            ],
            'sub_title' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'title.unique' => 'Judul sudah digunakan.',
            'sub_title.required' => 'Sub-judul wajib diisi.',
            'sub_title.string' => 'Sub-judul harus berupa teks.',
            'sub_title.max' => 'Sub-judul maksimal :max karakter.',
        ];
    }
}
