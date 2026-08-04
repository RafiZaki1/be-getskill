<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Contracts\Interfaces\Course\QuizInterface;
use App\Contracts\Interfaces\UserQuizInterface;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuizRequest;
use App\Http\Requests\UserQuizRequest;
use App\Http\Resources\ModuleQuestionNoAnswerResource;
use App\Http\Resources\QuizResource;
use App\Http\Resources\ResultResource;
use App\Http\Resources\UserQuizResource;
use App\Models\Module;
use App\Models\ModuleQuestion;
use App\Models\Quiz;
use App\Models\UserQuiz;
use App\Services\QuizService;
use App\Traits\PaginationTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class QuizController extends Controller
{
    use PaginationTrait;
    private QuizInterface $quiz;
    private ModuleInterface $module;
    private UserQuizInterface $userQuiz;
    private ModuleQuestionInterface $moduleQuestion;

    private QuizService $service;
    public function __construct(QuizInterface $quiz, ModuleQuestionInterface $moduleQuestion, QuizService $service, UserQuizInterface $userQuiz, ModuleInterface $module)
    {
        $this->quiz = $quiz;
        $this->module = $module;
        $this->userQuiz = $userQuiz;
        $this->moduleQuestion = $moduleQuestion;
        $this->service = $service;
    }
    /**
     * Method index
     *
     * @return JsonResponse
     */
    public function slug(string $slug): JsonResponse
    {
        try {
            $module = $this->module->showWithSlug($slug);
            $quiz = $module->quizzes->first();
            return ResponseHelper::success(QuizResource::make($quiz), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * index
     *
     * @param  mixed $slug
     * @return JsonResponse
     */
    public function index(string $slug): JsonResponse
    {
        $module = $this->module->showWithSlug($slug);
        if ($module->quizzes->first()) {
            $quiz = $module->quizzes->first();
            return ResponseHelper::success(QuizResource::make($quiz), trans('alert.fetch_success'));
        } else {
            return ResponseHelper::error(null, 'Quiz belum ada');
        }
    } 

    /**
     * show
     *
     * @param  mixed $request
     * @param  mixed $quiz
     * @return JsonResponse
     */
    public function show(Request $request, Quiz $quiz): JsonResponse
    {
        try {
            $userQuiz = $this->service->quiz($quiz);
            if ($userQuiz == 'failed') {
                return ResponseHelper::error(null, 'Anda sudah mengerjakan quiz ini, silahkan lanjutkan ke materi selanjutnya');
            }
            $request->merge(['id' => $userQuiz['questions']]);
    
            $moduleQuestions = $this->moduleQuestion->customPaginate($request);
            $data['paginate'] = $this->customPaginate($moduleQuestions->currentPage(), $moduleQuestions->lastPage());
            $data['quiz'] = QuizResource::make($quiz);
            $data['data'] = ModuleQuestionNoAnswerResource::collection($moduleQuestions);
            $data['user_quiz'] = UserQuizResource::make($userQuiz['userQuiz']);
    
            return responsehelper::success($data, trans('alert.fetch_success'));
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method submit
     *
     * @param UserQuizRequest $request [explicite description]
     * @param UserQuiz $userQuiz [explicite description]
     *
     * @return JsonResponse
     */
    public function submit(UserQuizRequest $request, UserQuiz $userQuiz): JsonResponse
    {
        $submit = $this->service->submit($request, $userQuiz);
        if ($submit == 'failed') {
            return ResponseHelper::error(null, 'masih ada delay waktu tersisa sebelum anda dapat melakukan submit kembali');
        }
        return ResponseHelper::success(true, trans('alert.fetch_success'));
    }

    /**
     * Method result
     *
     * @return JsonResponse
     */
    public function result(UserQuiz $userQuiz): JsonResponse
    {
        try {
            $result = $this->userQuiz->show($userQuiz->id);
            return ResponseHelper::success(ResultResource::make($result), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * get
     *
     * @return JsonResponse
     */
    public function get(): JsonResponse
    {
        try {
            $quizzes = $this->quiz->get();
            return ResponseHelper::success(QuizResource::collection($quizzes), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans(null, trans('alert.fetch_failed') . '. ' . $th->getMessage()));
        }
    }
    #[OA\Post(
        path: "/api/quizzes/{module}",
        operationId: "adminQuizStore",
        summary: "Pengaturan quiz pada modul (tambah/edit) (Admin)",
        description: "Menambahkan atau mengedit pengaturan quiz pada modul",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Quizzes"]
    )]
    #[OA\Parameter(name: "module", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "minimum_score", type: "integer", example: 75),
                new OA\Property(property: "duration", type: "integer", example: 30),
                new OA\Property(property: "max_attempts", type: "integer", example: 3)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Berhasil mengatur quiz")]
    public function store(QuizRequest $request, Module $module): JsonResponse
    {
        $data = $request->validated();
        try {
            $moduleId = $module->id ?? $request->module_id;

            if (!$moduleId) {
                return ResponseHelper::error(null, 'Module ID tidak ditemukan. Harap sertakan di URL atau di dalam body request.');
            }

            $this->service->checkIsAvailableQuiz($request, $moduleId);

            $data['module_id'] = $moduleId;
            $this->quiz->store($data);
            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }
    
    #[OA\Delete(
        path: "/api/quizzes/{quiz}",
        operationId: "adminQuizDestroy",
        summary: "Hapus quiz pada modul (Admin)",
        description: "Merubah ulang atau menghapus quiz pada modul",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Quizzes"]
    )]
    #[OA\Parameter(name: "quiz", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Berhasil menghapus quiz")]
    public function destroy(Quiz $quiz): JsonResponse
    {
        try {
            $this->quiz->delete($quiz->id);
            return ResponseHelper::success(null, trans('alert.delete_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.delete_failed') . '. ' . $th->getMessage());
        }
    }
}
