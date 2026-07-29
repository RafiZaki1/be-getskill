<?php
namespace App\Services\Course;

use App\Contracts\Repositories\Course\SubmissionTaskRepository;
use App\Models\ModuleTask;
use App\Models\SubmissionTask;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ModuleTaskService
{
    private SubmissionTaskRepository $submissionTask;

    public function __construct(SubmissionTaskRepository $submissionTask)
    {
        $this->submissionTask = $submissionTask;
    }

    public function getStudentWithSubmissionTask(mixed $data, ModuleTask $moduleTask)
    {
        $data->getCollection()->transform(function ($studentClassroom) use ($moduleTask) {
            $submissionTask = $studentClassroom->student->user->submissionTasks
                ->where('module_task_id', $moduleTask->id)
                ->first();

            $status = ! $submissionTask
                ? 'belum mengumpulkan'
                : (! $submissionTask->grade ? 'belum dinilai' : 'sudah dinilai');

            $studentClassroom->setAttribute('submissionTask', $submissionTask);
            $studentClassroom->setAttribute('status', $status);

            return $studentClassroom;
        });

        return $data;
    }

    /**
     * Membuat file ZIP dari semua submission task dalam 1 module task.
     *
     * @param  ModuleTask  $moduleTask
     * @return string|null  Path file ZIP atau null jika gagal
     */
    public function createZipOfAllSubmissions(ModuleTask $moduleTask): string
    {
        // Ambil semua submission beserta user
        $submissions = $moduleTask->submissionTask()->with('user')->get();

        if ($submissions->isEmpty()) {
            throw new \Exception("Tidak ada file tugas untuk module");
        }

        // Pastikan folder temp ada
        $tempFolder = storage_path('app/public/temp');
        if (! is_dir($tempFolder)) {
            mkdir($tempFolder, 0777, true);
        }

        // Nama file unik
        $zipFileName = 'all_module_assignments_' . time() . '.zip';
        $zipPath     = $tempFolder . '/' . $zipFileName;

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Gagal membuka file ZIP di path: {$zipPath}");
        }

        foreach ($submissions as $submission) {
            if ($submission->file && Storage::disk('public')->exists($submission->file)) {
                $filePath    = Storage::disk('public')->path($submission->file);
                $studentName = str_replace(' ', '_', strtolower($submission->user->name));
                $fileName    = $studentName . '_' . basename($submission->file);
                $zip->addFile($filePath, $fileName);
            }
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Get Student With All grade Submission And Average grade
     * 
     * @param mixed $studentClassrooms
     * @param mixed $moduleTasks
     * @return LengthAwarePaginator
     */
    public function getStudentWithScoreSubmissionTask(mixed $studentClassrooms, mixed $moduleTasks): LengthAwarePaginator
    {
        $studentClassrooms->getCollection()->transform(function ($studentClassroom) use ($moduleTasks) {
            $grades = [];
            $sum = 0;
            $count = 0;

            foreach($moduleTasks as $index => $moduleTask) {
                $submissionTask = $this->submissionTask->getByStudentAndTask($studentClassroom->student->user_id, $moduleTask->id);
    
                $grade = $submissionTask?->grade ?? '-';

                $grades['task_' . ($index + 1)] = $grade;
                
                if (is_numeric($grade)) {
                    $sum += $grade;
                    $count++;
                }
            }

            $average = $count > 0 ? round($sum / $count, 2) : '-';

            $studentClassroom->setAttribute('average_grade', $average);
            $studentClassroom->setAttribute('grades', $grades);
            
            return $studentClassroom;
        });

        return $studentClassrooms;
    }
}
