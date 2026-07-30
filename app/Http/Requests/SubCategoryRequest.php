<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubCategoryRequest extends ApiRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_categories')->ignore($this->route('sub_category')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'name.unique' => 'Nama sudah digunakan'
        ];
    }

    public function prepareForValidation()
    {
        $input = $this->all();

        $pattern = '/<script.*?>.*?<\/script>|(on\w+=["\'].*?["\'])|<style.*?>.*?<\/style>|style=["\'].*?["\']/is';

        foreach ($input as $key => $value) {
            if (is_string($value) && preg_match($pattern, $value)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Illegal script or style detected!',
                    'invalid_input' => [$key => $value], 
                ], 400);
            }
        }
    }
}
