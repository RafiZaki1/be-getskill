<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoursesRequestRequest extends FormRequest
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
            'user_id'    => ['required', 'exists:users,id'],
            'title'      => ['required', 'string', 'max:255'],
            'occupation' => ['required', 'string', 'max:255'],
            'type'       => ['required', 'in:free,paid'],
            'price'      => ['nullable', 'integer', 'min:0', 'required_if:type,paid'],
            'link'       => ['required', 'url'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->type === 'free') {
            $this->merge(['price' => 0]);
        }
    }
}
