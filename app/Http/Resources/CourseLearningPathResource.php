<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseLearningPathResource extends JsonResource
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
            'course' => CustomCourseResource::make($this->course),
            'module_count' => $this->course->modules->count(),
            'learning_path' => $this->learningPath
        ];
    }
}
