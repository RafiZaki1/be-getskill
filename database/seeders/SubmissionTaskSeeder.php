<?php

namespace Database\Seeders;

use App\Models\ModuleTask;
use App\Models\Student;
use App\Models\SubmissionTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubmissionTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil beberapa module task
        $moduleTasks = ModuleTask::limit(10)->get();

        if ($moduleTasks->isEmpty()) {
            $this->command->info('Tidak ada ModuleTask. Jalankan ModuleTaskSeeder terlebih dahulu.');

            return;
        }

        // Ambil beberapa student yang memiliki user
        $students = Student::with('user')->limit(15)->get();

        if ($students->isEmpty()) {
            $this->command->info('Tidak ada Student. Lewati seeder ini.');

            return;
        }

        // Pastikan folder submissions ada
        $submissionsFolder = 'submissions';
        if (! Storage::disk('public')->exists($submissionsFolder)) {
            Storage::disk('public')->makeDirectory($submissionsFolder);
        }

        $sampleAnswers = [
            'Jawaban lengkap tentang konsep yang ditanyakan dengan penjelasan detail.',
            'Implementasi solusi yang benar sesuai dengan requirement yang diberikan.',
            'Analisis mendalam dengan identifikasi masalah dan rekomendasi solusi.',
            'Desain yang well-structured dan mengikuti best practices.',
            'Implementasi yang optimal dengan pertimbangan performa dan scalability.',
            'Jawaban yang komprehensif dengan contoh-contoh konkret.',
            'Solusi yang elegant dan mudah dipahami.',
            'Penjelasan yang jelas dan terstruktur dengan baik.',
        ];

        // Create dummy files
        $dummyFiles = [];
        for ($i = 1; $i <= 5; $i++) {
            $fileName = "submission_{$i}.txt";
            $filePath = "{$submissionsFolder}/{$fileName}";

            // Buat file dengan content
            $content = "Submission File #{$i}\n";
            $content .= "Student: [Student Name]\n";
            $content .= 'Date: '.now()->format('Y-m-d H:i:s')."\n";
            $content .= "---\n";
            $content .= $sampleAnswers[array_rand($sampleAnswers)]."\n";

            Storage::disk('public')->put($filePath, $content);
            $dummyFiles[] = $filePath;

            $this->command->line("✓ Created: {$filePath}");
        }

        $count = 0;
        foreach ($moduleTasks as $moduleTask) {
            // Setiap module task akan memiliki submission dari 5-8 student
            $selectedStudents = $students->random(min(6, $students->count()));

            foreach ($selectedStudents as $student) {
                // 80% submission memiliki file, 20% hanya text answer
                $hasFile = rand(1, 100) <= 80;

                $submission = SubmissionTask::create([
                    'id' => Str::uuid(),
                    'module_task_id' => $moduleTask->id,
                    'user_id' => $student->user->id,
                    'answer' => $sampleAnswers[array_rand($sampleAnswers)],
                    'file' => $hasFile ? $dummyFiles[array_rand($dummyFiles)] : null,
                    'grade' => rand(0, 1) === 1 ? rand(60, 100) : null, // 50% sudah dinilai, 50% belum
                ]);

                $count++;
            }
        }

        $this->command->info("SubmissionTask dummy data berhasil dibuat! Total: {$count} submission");
        $this->command->info('Dummy files created in storage/app/public/submissions/');
    }
}
