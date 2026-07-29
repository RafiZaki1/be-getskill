<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CleanOldZips extends Command
{
    protected $signature = 'zip:clean-old';
    protected $description = 'Hapus file ZIP dan folder lama di storage/temp yang lebih dari 3600 detik';

    public function handle()
    {
        $disk = Storage::disk(); 
        $tempDir = 'temp';

        if (!$disk->exists($tempDir)) {
            $this->info('Folder temp tidak ada.');
            return 0;
        }

        $now = time();
        $deleted = 0;

        $files = $disk->files($tempDir);
        $folders = $disk->directories($tempDir);

        // Hapus file lama
        foreach ($files as $file) {

            $fullPath = $disk->path($file);

            if (file_exists($fullPath) && filemtime($fullPath) < $now - 3600) {
                unlink($fullPath); 
                $deleted++;
            }
        }

        // Hapus folder lama
        foreach ($folders as $folder) {

            $fullPath = $disk->path($folder);

            if (is_dir($fullPath) && filemtime($fullPath) < $now - 3600) {
                File::deleteDirectory($fullPath);
                $deleted++;
            }
        }

        $this->info("Selesai. Item lama yang dihapus: $deleted");

        return 0;
    }
}
