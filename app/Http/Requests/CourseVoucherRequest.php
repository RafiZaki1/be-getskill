<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseVoucherRequest extends ApiRequest
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
            'code' => 'required|string|max:255|unique:course_vouchers,code',
            'discount' => 'required|integer|between:1,100',
            'usage_limit' => 'required|integer|min:1',
            'start' => 'required',
            'end' => 'required',
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
            'code.required' => 'Kode wajib diisi',
            'code.unique' => 'Kode sudah digunakan',
            'discount.required' => 'Diskon wajib diisi',
            'discount.between' => 'Diskon antara 1 sampai 100',
            'usage_limit.min' => 'Batas penggunaan minimal :min',
            'usage_limit.required' => 'Batas Penggunaan wajib diisi',
            'start.required' => 'Tanggal mulai wajib diisi',
            'end.required' => 'Tanggal Berakhir wajib diisi',
        ];
    }
}
