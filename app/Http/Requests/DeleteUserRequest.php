<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends ApiRequest
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
            'user_id' => 'required|array|min:1',
            'user_id.*' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User wajib diisi.',
            'user_id.array' => 'Format user tidak valid.',
            'user_id.min' => 'User minimal diisi 1.',
            'user_ud.*.required' => 'ID user wajib diisi.',
            'user_id.*.exists' => 'ID user tidak valid.',
        ];
    }
}
