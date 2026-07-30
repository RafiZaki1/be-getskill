<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends ApiRequest
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
            'description' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:255',
            'twitter' => 'required|string|max:255',
            'facebook' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
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
            'description.required' => 'Deskripsi wajib diisi',
            'description.max' => 'Deskripsi maksimal :max karakter',
            'whatsapp.required' => 'Whatsapp wajib diisi',
            'whatsapp.max' => 'Whatsapp maksimal 255 karakter',
            'twitter.required' => 'twitter wajib diisi',
            'twitter.max' => 'twitter maksimal 255 karakter',
            'facebook.required' => 'facebook wajib diisi',
            'facebook.max' => 'facebook maksimal 255 karakter',
            'email.required' => 'email wajib diisi',
            'email.max' => 'email maksimal 255 karakter',
            'phone_number.required' => 'Nomor Telepon wajib diisi',
            'phone_number.max' => 'Nomor Telepon maksimal 255 karakter',
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
