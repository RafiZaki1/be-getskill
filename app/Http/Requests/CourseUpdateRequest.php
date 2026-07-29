<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseUpdateRequest extends ApiRequest
{

    /**
     * rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'sub_category_id' => 'required|exists:sub_categories,id',
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses')->ignore($this->route('course')),
            ],
            'sub_title' => 'required|string|max:255',
            'description' => 'required|string',
            'is_premium' => 'required|boolean',
            'price' => [
                'nullable',
                'integer',
                'min:0',
                Rule::requiredIf($this->input('is_premium') === true),
            ],
            'photo' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'promotional_price' => 'nullable|integer|min:0'
        ];
    }


    /**
     * messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'photo.mimes' => 'Photo harus berformat png, jpg, atau jpeg.',
            'photo.max' => 'Ukuran photo maksimal 2MB.',
            'sub_category_id.required' => 'Sub-kategori wajib diisi.',
            'sub_category_id.exists' => 'Sub-kategori tidak valid.',
            'title.required' => 'Judul wajib diisi.',
            'title.unique' => 'Judul sudah digunakan.',
            'sub_title.required' => 'Sub-judul wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'is_premium.required' => 'Status premium wajib diisi.',
            'is_premium.boolean' => 'Status premium harus berupa true atau false.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'promotional_price.integer' => 'Harga promo harus berupa angka.',
            'promotional_price.min' => 'Harga promo tidak boleh negatif.',
        ];
    }
}
