<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleListResource extends JsonResource
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
            'sub_title' => $this->sub_title,
            'module_question_count' => $this->moduleQuestions->count(),
            'sub_module_count' => $this->subModules->count(),
            'module_task_count' => $this->moduleTasks->count(),
        ];
    }
}
