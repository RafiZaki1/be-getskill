<?php

namespace App\Services\Course;

use App\Base\Interfaces\uploads\ShouldHandleFileUpload;
use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Enums\UploadDiskEnum;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Module;
use App\Models\User;
use App\Traits\UploadTrait;

class ContentImageService implements ShouldHandleFileUpload
{
    use UploadTrait;

    public function delete($unusedImage)
    {
        foreach ($unusedImage as $image) {
            $this->remove($image->path);
            $image->delete();
        }
    }
}
