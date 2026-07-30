<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->loadMissing(['subCategory', 'courseReviews']);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'slug' => $this->slug,
            'photo' => ($this->photo && Storage::exists($this->photo)) ? url('storage/' . $this->photo) : null,
            'is_premium' => $this->is_premium,
            'price' => $this->price,
            'promotional_price' => $this->promotional_price,
            'rating' => number_format($this->courseReviews->avg('rating') ?? 0, 1),
            'modules_count' => $this->modules()->count(),
            'sub_category' => $this->subCategory->name ?? '-',
            'course_review_count' => $this->courseReviews->count(),
            'is_ready' => $this->is_ready,
            'user_courses_count' => $this->userCourses->count(),
            'modules_count' => $this->modules->count(),
        ];
    }
}
