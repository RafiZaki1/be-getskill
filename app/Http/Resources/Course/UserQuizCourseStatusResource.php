<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserQuizCourseStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $quizzes = $this['quizzes'];
        $userQuizzes = $this['user_quizzes'];

        $completedQuizIds = $userQuizzes
            ->whereNotNull('score')
            ->pluck('quiz_id')
            ->toArray();

        $allQuizzesCompleted = $quizzes->isNotEmpty() && $quizzes->every(fn ($quiz) => in_array($quiz->id, $completedQuizIds));

        return [
            'course_test_id' => $this['course_test'] ? $this['course_test']->id : null,
            'all_quizzes_completed' => $allQuizzesCompleted,
            'total_quizzes' => $quizzes->count(),
            'completed_quizzes' => count($completedQuizIds),
        ];
    }
}
