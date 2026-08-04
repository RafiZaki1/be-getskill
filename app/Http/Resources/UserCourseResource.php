<?php

namespace App\Http\Resources;

use App\Helpers\CourcePercentaceHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use CourcePercentaceHelper for consistent percentage calculation
        $percentage = CourcePercentaceHelper::getPercentace($this);

        // Calculate tasks that are not submitted by the student
        $unsubmittedTasks = $this->getUnsubmittedTasks();
        $gradedTasks = $this->getGradedTasks();
        $ungradedTasks = $this->getUngradedTasks();

        return [
            'user' => $this->user,
            'course' => [
                'id' => $this->course->id,
                'title' => $this->course->title,
                'sub_category' => $this->course->subCategory,
                'user' => $this->course->user,
                'slug' => $this->course->slug,
                'thumbnail' => $this->course->photo,
                'price' => $this->course->price,
            ],
            'total_module' => $this->course->modules->count(),
            'total_user' => $this->course->userCourses->count(),
            'study_time' => $this->created_at
                ? now()->diffInHours($this->created_at) . ' jam ' . now()->diffInMinutes($this->created_at) % 60 . ' menit'
                : 'Belum ada waktu belajar',
            'study_percentage' => $percentage, // Use exact percentage, don't round it further
            'sub_module' => $this->subModule,
            'has_post_test' => $this->has_post_test,
            'has_pre_test' => $this->has_pre_test,
            'sub_module_slug' => $this->subModule?->slug,

            // Add total unsubmitted tasks to the response
            'unsubmitted_tasks' => $unsubmittedTasks,
            'graded_tasks' => $gradedTasks,
            'ungraded_tasks' => $ungradedTasks,

            'total_sub_module' => $this->course->modules->sum('sub_modules_count'),
            'completed_sub_modules' => $this->getCompletedSubModules(),
            'sub_module_step' => $this->subModule?->step,
            'max_sub_module_step' => $this->subModule?->max_step ?? 1,
            'total_quiz' => $this->course->modules->flatMap->quizzes->count(),
            'completed_quizzes' => $this->getCompletedQuizzes(),

            // Add raw values for debugging
            '_debug_has_post_test_raw' => $this->has_post_test,
            '_debug_has_post_test_type' => gettype($this->has_post_test),

        ];
    }

    /**
     * Calculate the number of tasks that have not been submitted by the student.
     *
     * @return int
     */
    private function getUnsubmittedTasks()
    {
        $unsubmittedTasks = 0;

        // Loop through each module and count tasks that have no submissions
        foreach ($this->course->modules as $module) {
            foreach ($module->moduleTasks as $task) {
                // Check if there is no submission for this task by the user
                $submissionExists = $task->submissionTask()->where('user_id', $this->user_id)->exists();
                if (!$submissionExists) {
                    $unsubmittedTasks++;
                }
            }
        }

        return $unsubmittedTasks;
    }

    /**
     * Calculate the number of tasks that have been graded.
     *
     * @return int
     */
    private function getGradedTasks()
    {
        $gradedTasks = 0;

        foreach ($this->course->modules as $module) {
            foreach ($module->moduleTasks as $task) {
                $gradedExists = $task->submissionTask()
                    ->where('user_id', $this->user_id)
                    ->whereNotNull('grade')
                    ->exists();

                if ($gradedExists) {
                    $gradedTasks++;
                }
            }
        }

        return $gradedTasks;
    }

    /**
     * Calculate the number of tasks that have been submitted but not graded.
     *
     * @return int
     */
    private function getUngradedTasks()
    {
        $ungradedTasks = 0;

        foreach ($this->course->modules as $module) {
            foreach ($module->moduleTasks as $task) {
                $ungradedExists = $task->submissionTask()
                    ->where('user_id', $this->user_id)
                    ->whereNull('grade')
                    ->exists();

                if ($ungradedExists) {
                    $ungradedTasks++;
                }
            }
        }

        return $ungradedTasks;
    }

    /**
     * Calculate the number of completed sub-modules.
     *
     * @return int
     */
    private function getCompletedSubModules()
    {
        $completed = 0;
        
        if (!$this->subModule) {
            return 0; // If no submodule is active, no sub-modules are completed
        }

        foreach ($this->course->modules as $module) {
            foreach ($module->subModules as $subModule) {
                if (
                    $subModule->id < $this->subModule->id ||
                    ($subModule->id == $this->subModule->id && $this->subModule->step >= $subModule->max_step)
                ) {
                    $completed++;
                }
            }
        }
        return $completed;
    }

    /**
     * Calculate the number of completed quizzes.
     *
     * @return int
     */
    private function getCompletedQuizzes()
    {
        return $this->course->modules->flatMap(function ($module) {
            return $module->quizzes->filter(function ($quiz) {
                return $quiz->userQuizzes->count() > 0;
            });
        })->count();
    }
}
