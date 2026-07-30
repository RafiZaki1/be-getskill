<?php

namespace App\Http\Resources;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseTestResource extends JsonResource
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
            'duration' => $this->duration,
            'total_question' => $this->total_question,
            'is_submitted' => $this->is_submitted,
            'course' => [
                'id' => $this->course->id,
                'sub_category' => $this->course->subCategory?->name,
                'course_test_id' => $this->courseTest ? $this->courseTest->id : null,
                'category' => $this->course->subCategory?->category?->name,
                'title' => $this->course->title,
                'sub_title' => $this->course->sub_title,
                'description' => $this->course->description,
                'slug' => $this->course->slug,
                'is_premium' => $this->course->is_premium,
                'price' => $this->course->price,
                'promotional_price' => $this->course->promotional_price,
                'photo' => ($this->course->photo && Storage::exists($this->course->photo)) ? url('storage/' . $this->course->photo) : null,
                'modules_count' => $this->course->modules->count(),
                'rating' => number_format($this->course->courseReviews->avg('rating'), 1) ?? 0,
                'course_review_count' => $this->course->courseReviews->count(),
                'user_courses_count' => $this->course->userCourses->count(),
                'created' => $this->course->created_at ? $this->course->created_at->format('d/m/Y') : null,
                'is_ready' => $this->course->is_ready
            ],
            'courseTestQuestions' => CourseTestQuestionResource::collection($this->courseTestQuestions)
        ];
    }
}
