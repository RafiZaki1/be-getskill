<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class CourseModuleNoDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "slug" => $this->slug,
            "modules" => $this->modules->map(function($module) {
                return [
                    'id' => $module->id,
                    'title' => $module->title,
                    'step' => $module->step,
                    'slug' => $module->slug,
                    'sub_module_count' => $module->subModules->count(),
                    'module_task_count' => $module->moduleTasks->count(),
                ];
            }),
        ];
    }
}
