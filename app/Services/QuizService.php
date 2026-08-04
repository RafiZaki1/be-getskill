<?php

namespace App\Services;

use App\Base\Interfaces\uploads\ShouldHandleFileUpload;
use App\Contracts\Interfaces\BlogInterface;
use App\Contracts\Interfaces\BlogViewInterface;
use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Contracts\Interfaces\Course\QuizInterface;
use App\Contracts\Interfaces\EventDetailInterface;
use App\Contracts\Interfaces\EventInterface;
use App\Contracts\Interfaces\UserQuizInterface;
use App\Enums\UploadDiskEnum;
use App\Http\Requests\BlogRequest;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\EventRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\QuizRequest;
use App\Http\Requests\UserQuizRequest;
use App\Models\Blog;
use App\Models\Event;
use App\Models\EventDetail;
use App\Models\ModuleQuestion;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuiz;
use App\Traits\UploadTrait;
use Exception;
use Illuminate\Http\Request;
use Spatie\ErrorSolutions\SolutionProviders\Laravel\MissingAppKeySolutionProvider;

class QuizService implements ShouldHandleFileUpload
{

    use UploadTrait;
    private UserQuizInterface $userQuiz;
    private QuizInterface $quiz;
    private ModuleQuestionInterface $moduleQuestion;
    public function __construct(UserQuizInterface $userQuiz, QuizInterface $quiz, ModuleQuestionInterface $moduleQuestion)
    {
        $this->quiz = $quiz;
        $this->moduleQuestion = $moduleQuestion;
        $this->userQuiz = $userQuiz;
    }
    public function store(Quiz $quiz)
    {

        $userQuiz = $quiz->userQuizzes()->where('user_id', auth()->user()->id)->latest()->first();
        if ($userQuiz) {
            if (intval($userQuiz->score) < $quiz->minimum_score && $userQuiz->has_submitted) {
                $questions = ModuleQuestion::query()->where('module_id', $quiz->module_id)->inRandomOrder()->limit($quiz->total_question)->get();
                $moduleIds = $questions->pluck('id')->toArray();
                $module_question_id = implode(',', $moduleIds);

                $userQuizData = [
                    'module_question_id' => $module_question_id,
                    'quiz_id' => $quiz->id,
                    'user_id' => auth()->user()->id
                ];

                $this->userQuiz->store($userQuizData);
            }
            $moduleIds = explode(',', $userQuiz->module_question_id);
            $questions = ModuleQuestion::query()
                ->where('module_id', $quiz->module_id)
                ->whereIn('id', $moduleIds)
                ->get()
                ->sortBy(callback: fn($question) => array_search($question->id, $moduleIds))
                ->values();
        } else {
            $questions = ModuleQuestion::query()->where('module_id', $quiz->module_id)->inRandomOrder()->limit($quiz->total_question)->get();
            $moduleIds = $questions->pluck('id')->toArray();
            $module_question_id = implode(',', $moduleIds);
            $userQuizData = [
                'module_question_id' => $module_question_id,
                'quiz_id' => $quiz->id,
                'user_id' => auth()->user()->id
            ];

            $userQuiz = $this->userQuiz->store($userQuizData);
        }


        return [
            'questions' => $questions,
            'userQuiz' => $userQuiz
        ];
    }
    public function quiz(Quiz $quiz)
    {
        try {
            $userQuiz = $quiz
                ->userQuizzes()
                ->where('user_id', auth()->user()->id)
                ->latest()
                ->firstOrFail();
            if ($userQuiz->score == null) {
                return [
                    'userQuiz' => $userQuiz,
                    'questions' => explode(',', $userQuiz->module_question_id)
                ];
            } else if ($userQuiz->score >= $quiz->minimum_score) {
                return 'failed';
            } else if ($userQuiz->score < $quiz->minimum_score) {
                $questions = $this->moduleQuestion->getQuestions($quiz->module_id, $quiz->total_question);
                $module_question_id = implode(',', $questions->pluck('id')->toArray());
                $data = [];
                $data['module_question_id'] = $module_question_id;
                $data['user_id'] = auth()->user()->id;
                $data['quiz_id'] = $quiz->id;
                $userQuiz = $this->userQuiz->store($data);
                return [
                    'userQuiz' => $userQuiz,
                    'questions' => explode(',', $userQuiz->module_question_id)
                ];
            }
        } catch (\Throwable $e) {
            $questions = $this->moduleQuestion->getQuestions($quiz->module_id, $quiz->total_question);
            $module_question_id = implode(',', $questions->pluck('id')->toArray());
            $data = [];
            $data['module_question_id'] = $module_question_id;
            $data['user_id'] = auth()->user()->id;
            $data['quiz_id'] = $quiz->id;
            $userQuiz = $this->userQuiz->store($data);
            return [
                'userQuiz' => $userQuiz,
                'questions' => explode(',', $userQuiz->module_question_id)
            ];
        }
    }


    public function submit(UserQuizRequest $request, UserQuiz $userQuiz)
    {
        $data = $request->validated();

        $answers = array_map(function ($answer) {
            return $answer == "" ? 'null' : $answer;
        }, $data['answer']);
        $questions = explode(',', $userQuiz->module_question_id);

        $correctCount = 0;

        foreach ($questions as $index => $questionId) {
            $moduleQuestion = ModuleQuestion::find($questionId);
            if (isset($answers[$index]) && $answers[$index] == $moduleQuestion->answer) {
                $correctCount++;
            }
        }

        $quiz = $userQuiz->quiz;
        $score = count(value: $questions) > 0 ? ($correctCount / count($questions)) * 100 : 0;
        $stringAnswer = implode(',', $answers);
        $userQuizData = [
            'score' => $score,
            'answer' => $stringAnswer,
            'has_submitted' => true,
        ];

        $timeAfterDelay = $userQuiz->updated_at->addMinutes($userQuiz->quiz->retry_delay);
        try {
            UserQuiz::where([
                'quiz_id' => $quiz->id,
                'user_id' => auth()->user()->id
            ])
                ->whereNotNull('score')
                ->latest()
                ->skip(1)
                ->firstOrFail();
            if (now() > $timeAfterDelay) {
                $this->userQuiz->update($userQuiz->id, $userQuizData);
            }
            return 'failed';
        } catch (\Throwable $e) {
            // UserQuiz::where(['quiz_id' => $quiz->id, 'user_id' => auth()->user()->id])->whereNull('score')->latest()->firstOrFail();
            $this->userQuiz->update($userQuiz->id, $userQuizData);
        }
    }

    public function checkIsAvailableQuiz(QuizRequest $request, string $module_id): void
    {
        $available = $this->moduleQuestion->getByModule($module_id)->count();
        if($available < $request->total_question){
            throw new Exception("Soal yang tersedia hanya {$available} soal.");
        }
    }
}
