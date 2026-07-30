<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use App\Http\Resources\QuizResource;
use App\Http\Resources\ModuleTaskResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ModuleTaskStudentStatusIsFinishResource;
use App\Http\Resources\SubModuleNoContentAndCourseResource;

class CourseModuleListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'step' => $this->step,
            'slug' => $this->slug,
            'sub_title' => $this->sub_title,
            'course' => [
                'id'    => $this->course->id,
                'title' => $this->course->title,
                'slug'  => $this->course->slug,
                'course_test_id'  => $this->course->courseTest?->id,
            ],
            'sub_module_count' => $this->subModules->count(),
            'module_task_count' => $this->moduleTasks->count(),
        ];
    }
}
