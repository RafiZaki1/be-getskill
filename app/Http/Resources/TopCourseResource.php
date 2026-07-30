<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopCourseResource extends JsonResource
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
            'user' => $this->user->name,
            'sub_category' => $this->subCategory->name,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'slug' => $this->slug,
            'price' => $this->price,
            'promotional_price' => $this->promotional_price,
            'photo' => url('storage/' . $this->photo),
            'rating' => $this->courseReviews->avg('rating') ?? 0,
            'user_courses_count' => $this->user_courses_count,
            'modules_count' => $this->modules_count,
        ];
    }
}
