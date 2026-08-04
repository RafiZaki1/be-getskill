<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleTaskRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => 'required|string|max:500',
            'point' => 'required|integer|min:1',
            'description' => 'required',
            'answer' => 'nullable'
        ];
    }

    /**
     * messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'question.required' => 'pertanyaan wajib diisi',
            'question.max' => 'Pertanyaan maksimal 500 karakter',
            'point.required' => 'Point wajib diisi',
            'point.min' => 'Point minimal 1',
            'point.integer' => 'Point harus angka',
            'description.required' => 'Deskripsi wajib diisi',
            'answer.nullable' => 'Kunci jawaban wajib diisi'
        ];
    }
}
