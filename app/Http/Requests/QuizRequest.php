<?php

namespace App\Http\Requests;

use App\Models\ModuleQuestion;
use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends ApiRequest
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
            'duration' => 'required|min:1',
            'rules' => 'required',
            'minimum_score' => 'required|min:1',
            'retry_delay' => 'required|min:1',
            'total_question' => [
                'required',
                'integer',
                'min:1'
            ],
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
            'duration.required' => 'Durasi wajib diisi',
            'duration.min' => 'Durasi minimal :min menit',
            'rules.required' => 'Peraturan wajib diisi',
            'total_question.required' => 'Total pertanyaan wajib diisi',
            'total_question.integer' => 'Total pertanyaan wajib berupa angka',
            'total_question.min' => 'Total pertanyaan minimal :min',
            'minimum_score.required' => 'Nilai minimal wajib diisi',
            'minimum_score.min' => 'Nilai minimal tidak boleh kurang dari :min',
            'retry_delay.required' => 'Waktu tunggu remidial harus diisi',
            'retry_delay.min' => 'Waktu tunggu remidial minimal :min menit',
            'minimum_score.integer' => 'Nilai minimal harus berupa angka',
            'retry_delay.integer' => 'Waktu tunggu harus berupa angka',
        ];
    }
}
