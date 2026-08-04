<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Format response.
 */
trait ChallengeTrait
{
    public static function makeDirectory($fileName)
    {
        $folderName = "challenge/{$fileName}";
        Storage::makeDirectory($folderName);
        return $folderName;
    }

    public static function upload($folder , $file)
    {
        $file->storeAs($folder, $file);
        return "{$folder}/{$file}";
    }

    
    public function renameFile(string $fileName, UploadedFile $file): mixed
    {
        $extension = $file->getClientOriginalExtension();
        $name = Str::slug($fileName);
        $customFileName = $name . '.' . $extension;
        return $customFileName;
    }

    public function remove(string $file): void
    {
        if ($this->exist($file)) Storage::delete($file);
    }

    public function exist(string $file): bool
    {
        return Storage::exists($file);
    }

}
