<?php

namespace App\Http\Resources;

use App\Models\ModuleQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
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

        $transformedQuestions = $this->transformQuestions($questions, $userAnswers);
        $totalCorrect = collect($transformedQuestions)->where('correct', true)->count();
        $totalFault = collect($transformedQuestions)->where('correct', false)->count();

        return [
            'id' => $this->id,
            'score' => number_format($this->score, 1),
            'total_fault' => $totalFault,
            'total_correct' => $totalCorrect,
            'status' => $this->score >= $this->quiz->minimum_score ? 'Lulus' : 'Tidak lulus',
            'total_question' => $totalFault + $totalCorrect,
            'questions' => $transformedQuestions,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Method to transform questions and append user answers
     */
    private function transformQuestions(array $questionIds, array $userAnswers): array
    {
        // $questions = ModuleQuestion::query()->whereIn('id', $questionIds)->get();

        $questions = ModuleQuestion::query()
            // ->where('module_id', $quiz->module_id)
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
