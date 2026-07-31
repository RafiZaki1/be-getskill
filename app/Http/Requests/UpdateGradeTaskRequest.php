<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Or implement your authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'grade' => 'nullable|numeric|min:0|max:100'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'grade.numeric' => 'Nilai harus berupa angka.',
            'grade.min' => 'Nilai tidak boleh kurang dari 0.',
            'grade.max' => 'Nilai tidak boleh lebih dari 100.'
        ];
    }
}