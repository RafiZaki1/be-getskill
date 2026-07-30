<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Sop;
use Illuminate\Database\Seeder; 

class SOPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sop::create([
            'role' => RoleEnum::MENTOR->value,
            'sop' => 'Mentor bertanggung jawab untuk memberikan bimbingan dan evaluasi berkala terhadap peserta.',
        ]);

        Sop::create([
            'role' => RoleEnum::TEACHER->value,
            'sop' => 'Teacher wajib menyampaikan materi pembelajaran sesuai dengan kurikulum dan memberikan tugas yang relevan.',
        ]);

        Sop::create([
            'role' => RoleEnum::STUDENT->value,
            'sop' => 'Student harus mengikuti seluruh kegiatan belajar, mengerjakan tugas, dan aktif dalam diskusi.',
        ]);
    }
}
