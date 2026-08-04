<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use App\Http\Resources\QuizResource;
use App\Http\Resources\ModuleTaskResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ModuleTaskStudentStatusIsFinishResource;
use App\Http\Resources\SubModuleNoContentAndCourseResource;

class ModuleSidebarResource extends JsonResource
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
            'step' => $this->step,
            'slug' => $this->slug,
            'sub_title' => $this->sub_title,
            'course' => [
                'id'    => $this->course->id,
                'title' => $this->course->title,
                'slug'  => $this->course->slug,
                'course_test_id'  => $this->course->courseTest?->id,
            ],
            'quizzes' => $this->quizzes->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'module_id' => $quiz->module_id,
                    'sub_module_slug_prev' => $quiz->sub_module_slug_prev,
                    'sub_module_slug_next' => $quiz->sub_module_slug_next,
                    'course_slug' => $quiz->module->course->slug,
                    'rules' => $quiz->rules,
                    'module_slug' => $quiz->module->slug,
                    'total_question' => $quiz->total_question,
                    'retry_delay' => $quiz->retry_delay,
                    'user_latest_quiz' => $quiz->userLatestQuiz,
                    'user_quiz_me' => $quiz->userQuizMe,
                    'user_quiz' => $quiz->userQuizzes()->where('user_id', auth()->id())->where('quiz_id', $quiz->id)->exists(),
                ];
            }),
            'quizz_count' => $this->quizzes->count(),
            'module_question_count' => $this->moduleQuestions->count(),
            'sub_modules' => SubModuleNoContentAndCourseResource::collection($this->subModules),
            'sub_module_count' => $this->subModules->count(),
            'module_tasks' => ModuleTaskStudentStatusIsFinishResource::collection($this->moduleTasks),
            'module_task_count' => $this->moduleTasks->count(),
            'is_done' => $this->is_done ?? null,
        ];
    }
}
