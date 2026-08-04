<?php

namespace App\Http\Resources;

use App\Models\ModuleQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResultResource extends JsonResource
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

        return [
            'id' => $this->id,
            'questions' => $this->transformQuestions($questions, $userAnswers),
            'answers' => $this->answer, // original answers
            'score' => number_format($this->score, 1),
        ];
    }

    /**
     * Method to transform questions and append user answers
     */
    private function transformQuestions(array $questionIds, array $userAnswers): array
    {
        $questions = ModuleQuestion::query()->whereIn('id', $questionIds)->get();

        return $questions->map(function ($question, $key) use ($userAnswers) {
            return [
                'question' => $question->question,
                'option_a' => $question->option_a,
                'option_b' => $question->option_b,
                'option_c' => $question->option_c,
                'option_d' => $question->option_d,
                'option_e' => $question->option_e,
                'correct_answer' => $question->answer,
                'user_answer' => $userAnswers[$key] ?? null, // append user answer
            ];
        })->toArray();
    }
}
