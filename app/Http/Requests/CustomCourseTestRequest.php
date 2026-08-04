<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomCourseTestRequest extends ApiRequest
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
            'module_id' => 'required|array',
            'total_question' => 'required|array'
        ];
    }
    /**
     * Method messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'module_id.required' => 'module wajib dipilih',
            'total_question.required' => 'total pertanyaan wajib diisi'
        ];
    }
}
