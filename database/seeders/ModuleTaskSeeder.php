<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleTask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ModuleTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil beberapa module yang ada
        $modules = Module::limit(5)->get();

        // Jika tidak ada module, skip
        if ($modules->isEmpty()) {
            $this->command->info('Tidak ada module. Jalankan ModuleSeeder terlebih dahulu.');

            return;
        }

        // Task dummy data
        $tasks = [
            ['question' => 'Jelaskan konsep OOP dalam pemrograman', 'description' => 'Tulis penjelasan lengkap tentang Object-Oriented Programming', 'point' => 100],
            ['question' => 'Buat function untuk menghitung nilai rata-rata array', 'description' => 'Implementasikan function dengan parameter array dan return nilai rata-rata', 'point' => 50],
            ['question' => 'Analisis kode program berikut dan identifikasi error', 'description' => 'Cari dan jelaskan error dalam kode yang disediakan', 'point' => 75],
            ['question' => 'Buat REST API endpoint untuk CRUD data siswa', 'description' => 'Implementasikan endpoint create, read, update, delete untuk data siswa', 'point' => 100],
            ['question' => 'Desain database schema untuk sistem e-learning', 'description' => 'Buat desain database dengan minimal 8 tabel dan 1 ERD diagram', 'point' => 150],
        ];

        $step = 1;
        foreach ($modules as $module) {
            foreach ($tasks as $index => $task) {
                ModuleTask::create([
                    'id' => Str::uuid(),
                    'module_id' => $module->id,
                    'step' => $step,
                    'question' => $task['question'],
                    'description' => $task['description'],
                    'point' => $task['point'],
                    'answer' => 'Jawaban referensi untuk task ini akan diisi oleh instruktur.',
                ]);
                $step++;
            }
        }

        $this->command->info('ModuleTask dummy data berhasil dibuat!');
    }
}
