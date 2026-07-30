<?php

namespace App\Http\Resources\Course;

use App\Http\Resources\ModuleTaskResource;
use App\Http\Resources\QuizResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleDetailCourseResource extends JsonResource
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
            'sub_title' => $this->sub_title,
            'course_id' => $this->course->id,
            'quizzes' => $this->quizzes->map(function ($quizzez) {
                return [
                    'module_slug' => $quizzez->module->slug,
                    'total_question' => $quizzez->total_question,
                ];
            }),
            'quizz_count' => $this->quizzes->count(),
            'sub_modules' => $this->subModules->map(function ($subModule) {
                return [
                    'id' => $subModule->id,
                    'title' => $subModule->title,
                ];
            }),
            'sub_module_count' => $this->subModules->count(),
            'module_task_count' => $this->moduleTasks->count(),
        ];
    }
}
