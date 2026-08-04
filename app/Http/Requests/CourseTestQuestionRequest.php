<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseTestQuestionRequest extends ApiRequest
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
            'module_id.*' => 'required',
            'question_count.*' => 'required|integer|min:1'
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
            'module_id.*.required' => 'id modul wajib diisi',
            'question_count.*.required' => 'total pertanyaan wajib diisi',
            'question_count.*.integer' => 'total pertanyaan harus berupa angka',
            'question_count.*.min' => 'total pertanyaan minimal :min',
        ];
    }
}
