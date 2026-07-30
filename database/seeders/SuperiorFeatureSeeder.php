<?php

namespace Database\Seeders;

use App\Models\SuperiorFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperiorFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SuperiorFeature::query()->create([
            'title' => 'Upgrade Skill Kamu dengan Hummaclass',
            'description' => 'belajar dari instruktur terbaik di kelas langsung terlibat, berinteraksi dan menyelesaikan keraguan',
            'mentor' => 'Mentor Kami ramah dan ahli dalam domain untuk membuat Anda belajar dengan mudah',
            'course' => 'Semua kursus kami dibuat dan untuk membuat Anda menikmati mempelajari hal-hal baru',
            'task' => 'Bergabunglah dengan kelas kami dengan alat interaktif dan dukungan keraguan'
        ]);
    }
}
