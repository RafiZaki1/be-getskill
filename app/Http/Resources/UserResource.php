<?php

namespace App\Http\Resources;

use App\Enums\RoleEnum;
use App\Enums\TestEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userCourses = $this->userCourses()->has('certificate')->get();
        $userEvents = $this->userEvents()->has('certificate')->get();

        $total_course_certificate = count($userCourses);
        $total_event_certificate = count($userEvents);
        $total_all_certificates = $total_course_certificate + $total_event_certificate;

        return [
            'id' => $this->id,
            'photo' => $this->photo !== null ? url('storage/' . $this->photo) : null,
            'name' => $this->name,
            'email' => $this->email,
            'points' => $this->point,
            'phone_number' => $this->phone_number,
            'user_courses' => $this->userCourses,
            'total_courses' => $this->userCourses->count(),
            'total_reviews' => $this->courseReviews->count(),
            'course_reviews' => ReviewResource::collection($this->courseReviews),
            // 'total_course_completed' => $this->userCourseTests()->where('type_test', TestEnum::POSTTEST->value)->whereNotNull('score')->count(),
            'total_certificate' => $total_all_certificates,
            'total_event_certificate' => $total_event_certificate,
            'total_course_certificate' => $total_course_certificate,
            'total_all_certificates' => $total_all_certificates,
            'course_activities' => $this->userCourses ? UserCourseResource::collection($this->userCourses) : null,
            'event_activities' => $this->userEvents ? UserEventResource::collection($this->userEvents) : null,
            'address' => $this->address,
            'banner' => $this->banner !== null ? url('storage/' . $this->banner) : null,
            'gender' => $this->gender,
            'created' => Carbon::parse($this->created_at)->format('d F Y'),
            'is_not_guest' => $this->hasRole(RoleEnum::STUDENT->value) || $this->hasRole(RoleEnum::MENTOR->value) || $this->hasRole(RoleEnum::TEACHER->value),
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'role' => $this->roles()->latest()->first()?->name,
        ];
    }
}
