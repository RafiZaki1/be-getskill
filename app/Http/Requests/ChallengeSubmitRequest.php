<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChallengeSubmitRequest extends ApiRequest
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
        $challenge = $this->route('challenge');

        return [
            'image' => [
                $challenge->image_active == 1 ? 'required' : 'nullable',
                'image'
            ],
            'file' => [
                $challenge->file_active == 1 ? 'required' : 'nullable',
                'file'
            ],
            'link' => [
                $challenge->link_active == 1 ? 'required' : 'nullable',
                'url'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Gambar wajib diisi.',
            'image.image' => 'Gambar tidak valid.',
            'file.required' => 'File wajib diisi.',
            'file.file' => 'File tidak valid.',
            'link.required' => 'Link wajib diisi.',
            'link.url' => 'Link tidak valid.',
        ];
    }
}
