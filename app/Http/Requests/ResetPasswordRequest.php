<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends ApiRequest
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
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
    
    public function messages(): array
    {
        return [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Panjang email maksimal :max karakter',
            'email.exists' => 'Email tidak ditemukan',
    
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password harus memiliki minimal :min karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password',
    
            'password_confirmation.required' => 'Konfirmasi password harus diisi',
            'password_confirmation.min' => 'Konfirmasi password harus memiliki minimal :min karakter',
        ];
    }    
}
