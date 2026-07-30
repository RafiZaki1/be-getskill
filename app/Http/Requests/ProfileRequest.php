<?php

namespace App\Http\Requests;

use App\Enums\GenderEnum;
use App\Helpers\ResponseHelper;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends ApiRequest
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
        $userId = $this->user()->id; // Fetch the authenticated user's ID

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'gender' => 'required|in:'. GenderEnum::MALE->value . ',' . GenderEnum::FEMALE->value,
            'banner' => 'nullable|mimes:png,jpg,jpeg|image',
            'photo' => 'nullable|mimes:png,jpg,jpeg|image',
            'bank' => 'nullable|string|max:100',
            'bank_number' => 'nullable|string|max:50',
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
            'name.required' => 'Nama wajib diisi',
            'name.string' => 'Nama harus berupa teks',
            'name.max' => 'Nama maksimal :max karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone_number.required' => 'Nomor telepon wajib diisi',
            'phone_number.string' => 'Nomor telepon harus berupa teks',
            'phone_number.max' => 'Nomor telepon maksimal :max karakter',
            'address.required' => 'Alamat wajib diisi',
            'address.string' => 'Alamat harus berupa teks',
            'photo.mimes' => 'Foto harus berupa file dengan ekstensi png, jpg, atau jpeg',
            'photo.image' => 'Foto harus berupa gambar',
            'banner.mimes' => 'Banner harus berupa file dengan ekstensi png, jpg, atau jpeg',
            'banner.image' => 'Banner harus berupa gambar',
            'gender.required' => "Jenis kelamin wajib diisi",
            'gender.in' => "Jenis kelamin tidak valid",
        ];
    }
}
