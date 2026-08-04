<?php

namespace App\Http\Resources\Course;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseReviewDetailCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $createdAt = Carbon::parse($this->created_at);

        return [
            'user_name' => $this->user->name,
            'user_photo' => $this->user->photo !== null ? url('storage/' . $this->user->photo) : null,
            'rating' => $this->rating,
            'review' => $this->review,
            'created' => $createdAt->diffForHumans(),
        ];
    }
}
