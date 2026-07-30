<?php

namespace App\Http\Resources;

use App\Models\ModuleQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCourseTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $moduleQuestionIds = explode(',', $this->module_question_id);


        $questions = ModuleQuestion::query()
            ->whereIn('id', $moduleQuestionIds)
            ->orderByRaw("FIELD(id, '" . implode("', '", $moduleQuestionIds) . "')")
            ->get('question');

        return [
            'id' => $this->id,
            'quiz_questions' => $questions->pluck('question'),
            'created_at' => $this->created_at,
            // 'quiz_answers' => $questions->pluck('nnanswer'),
        ];
    }
}
