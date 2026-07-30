<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleQuestionRequest extends ApiRequest
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
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'option_e' => 'required',
            'answer' => 'required',
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
            'question.required' => 'Pertanyaan wajib diisi',
            'option_a.required' => 'Opsi a wajib diisi',
            'option_b.required' => 'Opsi b wajib diisi',
            'option_c.required' => 'Opsi c wajib diisi',
            'option_d.required' => 'Opsi d wajib diisi',
            'option_e.required' => 'Opsi e wajib diisi',
            'answer.required' => 'Jawaban wajib diisi',
        ];
    }
}
