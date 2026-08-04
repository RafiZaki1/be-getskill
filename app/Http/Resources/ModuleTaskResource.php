<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray(Request $request): array
    {
        $token = substr($request->header('authorization') ?? '', 7, 100);
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;

        $this->loadMissing([
            'submissionTask' => function($q) use ($user) {
                $q->where('user_id', $user?->id)
                    ->select('id', 'module_task_id', 'user_id', 'grade');
            },
            'module.course',
        ]);

        $grades = $this->submissionTask->pluck('grade')->filter();

        return [
            'id' => $this->id,
            'module' => [
                'id' => $this->module->id,
                'course_id' => $this->module->course->id,
                'step' => $this->module->step,
                'title' => $this->module->title,
                'slug' => $this->module->slug,
                'sub_title' => $this->module->sub_title,
                'course' => [
                    'id' => $this->module->course->id,
                    'title' => $this->module->course->title,
                    'slug' => $this->module->course->slug,
                ],
            ],
            'question' => $this->question,
            'description' => $this->description,
            'point' => $this->point,
            
            'total_students' => $this->submissionTask->count(),
            'highest_score' => $grades->max(),
            'lowest_score' => $grades->min(),
            'average_score' => $grades->avg() ? round($grades->avg(), 2) : null,

            'submission_task' => SubmissionTaskResource::collection($this->submissionTask),

            'is_finish' => $user
                ? $this->submissionTask->contains('user_id', $user?->id)
                : false,

            'course_photo' => url('storage/' . $this->module->course->photo),
        ];
    }
}
