<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseTestRequest extends FormRequest
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
            'total_question' => 'required',
            'duration' => 'required',
            'is_submitted' => 'required|boolean',
            'module_id.*' => 'required',
            'question_count' => 'required',
            'module_id' => 'required',
            'question_count.*' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'module_id.required' => 'Modul wajib diisi',
            'question_count.required' => 'Total pertanyaan wajib diisi',
            'total_question.required' => 'Total Pertanyaan Wajib Diisi',
            'duration.required' => 'Durasi Wajib Wajib Diisi',
            'module_id.*.required' => 'Module wajib diisi',
            'question_count.*.required' => 'Total pertanyaan wajib diisi'
        ];
    }
}
