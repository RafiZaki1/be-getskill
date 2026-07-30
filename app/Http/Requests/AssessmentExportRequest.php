<?php

namespace App\Http\Requests;

class AssessmentExportRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'semester' => match ($this->semester) {
                'ganjil' => 1,
                'genap'  => 2,
                default  => $this->semester,
            },
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'school_year' => ['required', 'string'],
            'semester' => ['required', 'integer', 'in:1,2'],
            'school_ids' => ['required', 'array', 'min:1'],
            'school_ids.*' => ['required', 'uuid']
        ];
    }

    public function messages(): array
    {
        return [
            'school_year.required' => 'Tahun ajaran wajib diisi.',
            'semester.required' => 'Semester wajib diisi.',
            'school_ids.required' => 'Sekolah wajib dipilih.',
            'school_ids.array' => 'Sekolah harus berupa array.',
            'school_ids.min' => 'Pilih minimal satu sekolah.',
        ];
    }
}
