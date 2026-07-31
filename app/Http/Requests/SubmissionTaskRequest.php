<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionTaskRequest extends ApiRequest
{
    private $mimes = 'png,jpg,jpeg,zip,rar';
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
            'answer' => ['nullable',
            'url',
            'max:255'],
            'file' => 'nullable|mimes:' . $this->mimes . '|max:10240'
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
            'answer.url'   => 'Jawaban harus berupa URL yang valid.',
            'answer.max'   => 'Link terlalu panjang.',
            'answer.regex' => 'Harap masukkan link repository GitHub (github.com/user/repo).',
            'file.required' => 'File wajib diisi',
            'file.mimes' => 'File harus berformat ' . $this->mimes,
            'file.max' => 'Ukuran file maksimal adalah 10MB.',
        ];
    }
}
