<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class SetUserRoleRequest extends FormRequest
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
            'role' => 'required|in:' . RoleEnum::STUDENT->value . ',' . RoleEnum::TEACHER->value . ',' . RoleEnum::MENTOR->value,
            'name' => 'required|max:255',
            'school_id' => 'required_if:role,student,teacher|nullable|exists:schools,id',
        ];
    }
    
    public function messages(): array
    {
        return [
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role tidak valid',
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama tidak boleh lebih dari :max karakter',
            'school_id.required_if' => 'Silahkan pilih sekolah',
            'school_id.exists' => 'Sekolah tidak valid',
        ];
    }
}
