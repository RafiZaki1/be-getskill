<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\CourseTestInterface;
use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Contracts\Interfaces\UserQuizInterface;
use App\Contracts\Repositories\Course\QuizRepository;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserQuizRequest;
use App\Http\Resources\Course\UserQuizCourseStatusResource;
use App\Http\Resources\UserQuizResource;
use App\Http\Resources\UserQuizResultResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserQuizController extends Controller
{
    private UserQuizInterface $userQuiz;
    private ModuleInterface $module;
    private QuizRepository $quiz;
    private CourseTestInterface $courseTest;
    public function __construct(UserQuizInterface $userQuiz, ModuleInterface $module, QuizRepository $quiz, CourseTestInterface $courseTest)
    {
        $this->userQuiz = $userQuiz;
        $this->module = $module;
        $this->quiz = $quiz;
        $this->courseTest = $courseTest;
    }
    /**
     * Method index
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
        $userQuizzes = $this->userQuiz->get();
        return ResponseHelper::success(UserQuizResource::collection($userQuizzes), trans('alert.fetch_success'));
        }  catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        };
    }

    /**
     * getByUserCourse
     *
     * @param  mixed $courseSlug
     * @return JsonResponse
     */
    public function getByUserCourse(string $courseSlug): JsonResponse
    {
        try {
            $user = auth()->user();
            $courseTest = $this->courseTest->showWithSlug($courseSlug);
            $quizzes = $this->quiz->getByCourse($courseSlug);
            $userQuizzes = $this->userQuiz->getByUserAndCourseCompleted($user, $courseSlug);
            $data = [
                'course_test' => $courseTest,
                'quizzes' => $quizzes,
                'user_quizzes' => $userQuizzes,
            ];
            return ResponseHelper::success(new UserQuizCourseStatusResource($data), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }        
    }

    /**
     * getByUser
     *
     * @param  mixed $slug
     * @return JsonResponse
     */
    public function getByUser(string $slug): JsonResponse
    {
        try {
        $module = $this->module->showWithSlug($slug);
        $userQuizzes = $this->userQuiz->getWhere(['module_id' => $module->id]);
        return ResponseHelper::success(UserQuizResultResource::collection($userQuizzes), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method store
     *
     * @param UserQuizRequest $request [explicite description]
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validated();
        try {
        $this->userQuiz->store($data);
        return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }
}
