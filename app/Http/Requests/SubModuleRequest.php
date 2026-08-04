<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubModuleRequest extends ApiRequest
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
                Rule::unique('sub_modules')->ignore($this->route('sub_module')),
            ],
            'sub_title' => 'required|string|max:255',
            'content' => 'required|string',
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
            'content.required' => 'Konten wajib diisi.',
            'content.string' => 'Konten harus berupa teks.',
        ];
    }
}
