<?php

namespace App\Traits\Excel;

use App\Rules\GenderRule;
use App\Rules\ReligionRule;
use App\Rules\ResidenceStatusRule;
use Illuminate\Validation\Rule;

trait ValidationStudentTrait
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */

    public function rules(): array
    {
        return [
            'nama' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'alamat' => 'required',
            'no_hp' => 'required',
            'jenis_kelamin' => 'required',
            'kelas' => 'nullable',
            'nisn' => 'required|max:12',
            'tanggal_lahir' => 'required',
        ];
    }

    /**
     * Custom Validation Messages
     *
     * @return array<string, mixed>
     */

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom nama wajib diisi.',
            'nama.max' => 'Kolom nama tidak boleh lebih dari :max karakter.',
            'email.required' => 'Kolom email wajib diisi.',
            'email.max' => 'Kolom email tidak boleh lebih dari :max karakter.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'nisn.required' => 'Kolom NISN wajib diisi.',
            'nisn.max' => 'Kolom NISN tidak boleh lebih dari :max karakter.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'tahun_ajaran.required' => 'Kolom tahun ajaran wajib diisi.',
            'tahun_ajaran.exists' => 'Tahun ajaran yang dipilih tidak valid.',
            'alamat.required' => 'Kolom alamat wajib diisi.',
            'tanggal_lahir.required' => 'Kolom tanggal lahir wajib diisi.',
            'nik.required' => 'Kolom NIK wajib diisi.',
            'nik.max' => 'Kolom NIK tidak boleh lebih dari :max karakter.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'no_kk.required' => 'Kolom nomor KK wajib diisi.',
            'no_kk.max' => 'Kolom nomor KK tidak boleh lebih dari :max karakter.',
            'no_akta.required' => 'Kolom nomor akta kelahiran wajib diisi.',
            'no_akta.max' => 'Kolom nomor akta kelahiran tidak boleh lebih dari :max karakter.',
            'anak_ke.required' => 'Kolom anak ke wajib diisi.',
            'jumlah_saudara.required' => 'Kolom jumlah saudara wajib diisi.',
            'status_kewarganegaraan.required' => 'Kolom status kewarganegaraan wajib diisi.',
            'nis.required' => 'Kolom NIS wajib diisi.',
            'nis.max' => 'Kolom NIS tidak boleh lebih dari :max karakter.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'tempat_lahir.required' => 'Kolom tempat lahir wajib diisi.',
            'jenis_kelamin.required' => 'Kolom jenis kelamin wajib diisi.',
            'agama.required' => 'Kolom agama wajib diisi.',
        ];
    }
}
