<?php

namespace App\Services;

use App\Contracts\Interfaces\UserCourseTestInterface;
use App\Contracts\Repositories\Course\ModuleQuestionRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserCourseTestService
{
    private UserCourseTestInterface $userCourseTest;
    private ModuleQuestionRepository $moduleQuestion;

    // Inject the UserCourseTestInterface into the constructor
    public function __construct(UserCourseTestInterface $userCourseTest, ModuleQuestionRepository $moduleQuestion)
    {
        $this->userCourseTest = $userCourseTest;
        $this->moduleQuestion = $moduleQuestion;
    }

    /**
     * userLastStep
     *
     * @param  mixed $course
     * @param  mixed $subModule
     * @return void
     */
    public function search($query, Request $request)
    {
        return $query->when($request->search, function ($query) use ($request) {
            $query->whereRelation('user', 'name', 'LIKE', '%' . $request->search . '%');
        })->when($request->type, function ($query) use ($request) {
            $query->where('test_type', 'LIKE', '%' . $request->type . '%');
        })->get();
    }

    /**
     * Remove score for a given UserCourseTest
     *
     * @param mixed $userCourseTestId
     * @return mixed
     */
    public function removeScore(mixed $userCourseTestId): mixed
    {
        return $this->userCourseTest->removeScore($userCourseTestId);
    }

    /**
     * mapping for student test
     *
     * @param LengthAwarePaginator $userCourseTests
     * @return array
     */
    public function mappingDataForUserCourse(LengthAwarePaginator $userCourseTests): array
    {
        $highest = 0;
        $lowest = 100;
        $scoreTotal = 0;

        $userCourseTests->map(function($userCourseTest) use (&$highest, &$lowest, &$scoreTotal) {
            $questionIds = explode(',', $userCourseTest->module_question_id);
            $userAnswers = explode(',', $userCourseTest->answer);
            
            $questions = $this->moduleQuestion->whereIn($questionIds);
            $transformedQuestions = collect($this->transformQuestions($questions, $userAnswers));
    
            $totalQuestion = $transformedQuestions->count();
            $totalCorrect = $transformedQuestions->where('correct', true)->count();
            $totalFault = $totalQuestion - $totalCorrect;
    
            $userCourseTest->totalFault = $totalFault;
            $userCourseTest->totalCorrect = $totalCorrect;
            $userCourseTest->total = $totalQuestion;

            $highest = max($highest, $userCourseTest->score);
            $lowest = min($lowest, $userCourseTest->score);

            $scoreTotal += $userCourseTest->score;
        });

        $count = $userCourseTests->count();

        $data['header'] = [
            'count_student' => $count,
            'the_highest_score' => $highest,
            'the_lowest_score' => $count > 1 ? $lowest : 0,
            'average' => $count > 0 ? number_format($scoreTotal / $count, 1) : 0,
        ];

        $data['rawData'] = $userCourseTests;

        return $data;
    }

    /**
     * mapping correct question
     *
     * @param Collection $questions
     * @param array $userAnswers
     * @return array
     */
    private function transformQuestions(Collection $questions, array $userAnswers): array
    {
        return $questions->map(function ($question, $key) use ($userAnswers) {
            $userAnswer = $userAnswers[$key] ?? null;
            $correct = $userAnswer == $question->answer;

            return [
                'correct_answer' => $question->answer,
                'user_answer' => $userAnswer,
                'correct' => $correct,
            ];
        })->toArray();
    }
}
