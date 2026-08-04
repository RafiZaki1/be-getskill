<?php 

namespace App\Http\Resources;

use App\Http\Resources\Course\ModuleNoDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CertificateResource extends JsonResource
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
        'code' => $this->code,
        'username' => $this->username,
        'certificate' => $this->certificate ? url('storage/' . $this->certificate) : null,
        'user' => [
            'id' => $this->userCourse?->user->id,
            'name' => $this->userCourse?->user->name,
            'email' => $this->userCourse?->user->email,
            'photo' => $this->userCourse?->user->photo ? url('storage/' . $this->userCourse?->user->photo) : null,
        ],
        'course' => [
            'id' => $this->userCourse?->course->id,
            'title' => $this->userCourse?->course->title,
            'category' => $this->userCourse?->course->subCategory->category->name,
            'rating' => number_format($this->userCourse?->course->courseReviews->avg('rating'), 1) ?? 0,
            'sub_title' => $this->userCourse?->course->sub_title,
            'description' => $this->userCourse?->course->description,
            'slug' => $this->userCourse?->course->slug,
            'photo' => $this->userCourse?->course->photo ? url('storage/' . $this->userCourse?->course->photo) : null,
            'created' => $this->userCourse?->course->created_at,
            'user_courses_count' => $this->userCourse?->course->userCourses?->count(),
            // 'modules' => ModuleNoDetailResource::collection($this->userCourse?->course->modules),
        ],
        'created' => $this->created_at,
        'valid_until' => $this->expired_at,
    ];
}
}
