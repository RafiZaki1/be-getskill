<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseReviewRequest extends ApiRequest
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
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
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
            'review.max' => 'Ulasan maksimal :max karakter',
            'rating.required' => 'Rating wajib diisi',
            'rating.between' => 'Rating antara 1 sampai 5'
        ];
    }
}
