<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadTrait
{
    /**
     * delete specified file in storage
     * @param string $file
     * @return void
     */

    public function remove(string $file): void
    {
        if ($this->exist($file)) Storage::delete($file);
    }

    /**
     * check specified file in storage
     * @param string $file
     * @return bool
     */

    public function exist(string $file): bool
    {
        return Storage::exists($file);
    }

    /**
     * Handle upload file to storage
     * @param string $disk
     * @param UploadedFile $file
     * @param bool $originalName
     * @return string
     */

    public function upload(string $disk, UploadedFile $file, bool $originalName = false): string
    {
        try {
            $this->makeDirectory($disk);

            Log::info($originalName ? 'Uploading file with original name' : 'Uploading file without original name');

            $filePath = $originalName
                ? $disk . '/' . $file->getClientOriginalName()
                : $disk . '/' . uniqid() . '.' . $file->getClientOriginalExtension();

            $stream = fopen($file->getRealPath(), 'r');

            if (!$stream) {
                Log::error('Gagal membuka file stream.', [
                    'real_path' => $file->getRealPath(),
                    'filename' => $file->getClientOriginalName(),
                ]);
                return false;
            }

            $result = Storage::put($filePath, $stream);

            fclose($stream);

            if (!$result) {
                Log::error('Storage::put() gagal menyimpan file.', [
                    'path' => $filePath,
                    'disk' => $disk,
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'is_valid' => $file->isValid(),
                ]);
                return false;
            }

            return $filePath;
        } catch (\Throwable $th) {
            Log::error('File upload exception: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'disk' => $disk,
                'filename' => $file?->getClientOriginalName(),
            ]);

            return false;
        }
    }

    /**
     * uploadSlug
     *
     * @param  mixed $disk
     * @param  mixed $file
     * @param  mixed $slug
     * @param  mixed $originalName
     * @return string
     */
    public function uploadSlug(string $disk, UploadedFile $file, string $slug, bool $originalName = false): string
    {
        if (!Storage::exists($disk)) {
            Storage::makeDirectory($disk);
        }

        $slug = str_replace(' ', '-', $slug);
        $slug = str_replace(':', '-', $slug);

        $fileName = $originalName
            ? $file->getClientOriginalName()
            : Str::random(10) . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($disk, $fileName);
    }

    /**
     * Handle Make Directory if the folder doesn't exist
     * @param string $disk
     * @return void
     */

    public function makeDirectory(string $disk): void
    {
        if (!$this->exist($disk)) Storage::makeDirectory($disk);
    }
}
