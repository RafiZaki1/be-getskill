<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends ApiRequest
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
            'old_password' => ['required', function ($attribute, $value, $fail) {
                // Cek apakah password lama sesuai dengan yang ada di database
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('Password lama yang Anda masukkan tidak sesuai.');
                }
            }],
            'password' => 'required|min:8|confirmed',
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages()
    {
        return [
            'old_password.required' => "Password lama harus diisi.",
            'password.required' => "Password baru harus diisi.",
            'password_confirmation.required' => "Konfirmasi password harus diisi.",
            'password.min' => ":attribute minimal 8 karakter.",
            'password.confirmed' => "Konfirmasi password tidak sesuai.",
        ];
    }
}
