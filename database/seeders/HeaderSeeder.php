<?php

namespace Database\Seeders;

use App\Models\Header;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Header::query()->create([
            'title' => 'Tingkatkan Skill Anda Dengan Kursus di Hummaclass',
            'description' => 'Meningkatkan skill guru dan siswa dengan program kelas berbasis Industri.',
        ]);
    }
}
