<?php

namespace App\Http\Resources;

use App\Models\ModuleQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseTestResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $questions = explode(',', $this->module_question_id);
        $userAnswers = explode(',', $this->answer);

        $transformedQuestions = collect($this->transformQuestions($questions, $userAnswers));

        $total = $transformedQuestions->count();
        $totalCorrect = $transformedQuestions->where('correct', true)->count();
        $totalFault = $total - $totalCorrect;

        $classroom = $this->user?->student?->studentClassrooms?->first()?->classroom;

        return [
            'id' => $this->id,
            'user_id' => $this->user->id, 
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'user_classroom_name' => $classroom?->name ?? '-',
            'user_class_level' => $classroom?->class_level ?? '-',
            'user_photo' => asset('storage/' . $this->user->photo),
            'score' => number_format($this->score, 1),
            'test_type' => $this->test_type,
            'total_fault' => $totalFault,
            'total_correct' => $totalCorrect,
            'total_question' => $totalFault + $totalCorrect,
            'questions' => $transformedQuestions,
            'course_slug' => $this->courseTest->course->slug,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Method to transform questions and append user answers
     */
    private function transformQuestions(array $questionIds, array $userAnswers): array
    {
        $questions = ModuleQuestion::query()
            ->whereIn('id', $questionIds)
            ->get()
            ->sortBy(callback: fn($question) => array_search($question->id, $questionIds))
            ->values();
        return $questions->map(function ($question, $key) use ($userAnswers) {
            $userAnswer = $userAnswers[$key] ?? null;
            $correct = $userAnswer == $question->answer;

            return [
                'question' => $question->question,
                'option_a' => $question->option_a,
                'option_b' => $question->option_b,
                'option_c' => $question->option_c,
                'option_d' => $question->option_d,
                'option_e' => $question->option_e,
                'correct_answer' => $question->answer,
                'user_answer' => $userAnswer,
                'correct' => $correct,
            ];
        })->toArray();
    }
}
