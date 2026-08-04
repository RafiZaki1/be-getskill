<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubModuleResource extends JsonResource
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
            'module_id' => $this->module->id,
            'course_slug' => $this->module->course->slug,
            'module' => $this->module->title,
            'step' => $this->step,
            'last_step' => $this->module->subModules()->count(),
            'module_id' => $this->module->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'sub_title' => $this->sub_title,
            'content' => $this->content,
            'url_youtube' => $this->url_youtube,
            'course_id' => $this->module->course_id,
            'course_title' => $this->module->course->title,
            'course_slug' => $this->module->course->slug,
            'course_sub_title' => $this->module->course->sub_title,
            'course_sub_category' => $this->module->course->subCategory->category->name,
            'prev' => $this->prev->slug ?? null,
            'next' => $this->next->slug ?? null,
        ];
    }
}
