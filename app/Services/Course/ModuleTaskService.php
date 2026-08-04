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


}
