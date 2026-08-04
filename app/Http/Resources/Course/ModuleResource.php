<?php

namespace App\Http\Resources\Course;

use App\Http\Resources\ModuleTaskResource;
use App\Http\Resources\QuizResource;
use App\Http\Resources\SubModuleNoContentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
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
            'course' => $this->course,
            'quizzes' => QuizResource::collection($this->quizzes),
            'quizz_count' => $this->quizzes_count,
            'module_question_count' => $this->module_questions_count,
            'sub_modules' => SubModuleNoContentResource::collection($this->subModules),
            'sub_module_count' => $this->sub_modules_count,
            'module_tasks' => ModuleTaskResource::collection($this->moduleTasks),
            'module_task_count' => $this->module_tasks_count,
            'is_done' => $this->is_done ?? null,
        ];
    }
}
