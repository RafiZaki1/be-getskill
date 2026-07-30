<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->load(['user', 'subCategory.category', 'courseTest', 'modules', 'courseReviews', 'userCourses']);

        return [
            'id' => $this->id,
            'user' => new UserResource($this->user) ?? null,
            'sub_category' => $this->subCategory->name,
            'course_test_id' => $this->courseTest ? $this->courseTest->id : null,
            'category' => $this->subCategory->category->name,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'description' => $this->description,
            'slug' => $this->slug,
            'is_premium' => $this->is_premium,
            'price' => $this->price,
            'promotional_price' => $this->promotional_price,
            'photo' => ($this->photo && Storage::exists($this->photo)) ? url('storage/' . $this->photo) : null,
            'modules_count' => $this->modules->count(),
            'rating' => number_format($this->courseReviews->avg('rating'), 1) ?? 0,
            'course_reviews' => CourseReviewResource::collection($this->courseReviews),
            'course_review_count' => $this->courseReviews->count(),
            'user_courses_count' => $this->userCourses->count(),
            'created' => $this->created_at->format('d/m/Y'),
            'is_ready' => $this->is_ready
        ];
    }
}
