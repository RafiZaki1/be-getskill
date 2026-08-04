<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

class RegisterRequest extends ApiRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'recaptcha_token' => 'required',
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, silakan guna kan email lain.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.string' => 'Kata sandi harus berupa teks.',
            'password.min' => 'Kata sandi harus minimal 8 karakter.',
            'password.confirmed' => 'Kata sandi tidak cocok dengan password konfirmasi.',

            'password_confirmation.required' => 'Kata sandi wajib diisi.',
            'recaptcha_token.required' => 'Silahkan verifikasi captcha.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $response = Http::asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $this->recaptcha_token,
                ]
            );

            $result = $response->json();

            // reCAPTCHA v2 hanya mengembalikan 'success'
            if (!($result['success'] ?? false)) {
                $validator->errors()->add(
                    'recaptcha_token',
                    'Validasi captcha gagal.'
                );
            }
        });
    }
}
