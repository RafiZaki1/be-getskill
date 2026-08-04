<?php

namespace App\Services\Course;

use App\Base\Interfaces\uploads\ShouldHandleFileUpload;
use App\Enums\UploadDiskEnum;
use App\Http\Requests\CourseReviewRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Models\UserCourse;
use App\Traits\UploadTrait;

class CourseReviewService
{

    public function store(CourseReviewRequest $request, UserCourse $userCourse): array|bool
    {
        $data = $request->validated();
        $data['user_course_id'] = $userCourse->id;

    }
}
