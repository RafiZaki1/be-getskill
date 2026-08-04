<?php

namespace App\Http\Resources\Certificate;

use App\Http\Resources\Course\ModuleNoDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'category' => $this->subCategory?->category?->name,
            'sub_category' => $this->subCategory?->name,
            'rating' => number_format($this->courseReviews->avg('rating'), 1) ?? 0,
            'sub_title' => $this->sub_title,
            'description' => $this->description,
            'slug' => $this->slug,
            'photo' => $this->photo ? url('storage/' . $this->photo) : null,
            'created' => $this->created_at,
            'user_courses_count' => $this->userCourses->count(),
            'modules' => ModuleNoDetailResource::collection($this->modules),
        ];
    }
}
