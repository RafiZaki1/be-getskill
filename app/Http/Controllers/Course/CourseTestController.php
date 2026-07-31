<?php

namespace App\Http\Controllers\Course;

use App\Models\Course;
use App\Enums\TestEnum;
use App\Models\UserQuiz;
use App\Models\CourseTest;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Models\UserCourseTest;
use App\Helpers\ResponseHelper;
use App\Traits\PaginationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\CourseTestService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Requests\CourseTestRequest;
use App\Http\Resources\CourseTestResource;
use App\Http\Requests\UserCourseTestRequest;
use App\Http\Requests\CustomCourseTestRequest;
use App\Http\Resources\ModuleQuestionResource;
use App\Http\Resources\UserCourseTestResource;
use App\Http\Resources\CourseTestDetailResource;
use App\Http\Resources\CourseTestResultResource;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Contracts\Interfaces\UserCourseTestInterface;
use App\Http\Resources\ModuleQuestionStudentResource;
use App\Contracts\Interfaces\Course\CourseTestInterface;
use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Contracts\Interfaces\Course\CourseTestQuestionInterface;
use App\Contracts\Repositories\Course\UserCourseRepository;
use App\Helpers\CourcePercentaceHelper;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CourseTestController extends Controller
{
    use PaginationTrait;
    private CourseTestInterface $courseTest;
    private CourseTestQuestionInterface $courseTestQuestion;
    private CourseInterface $course;
    private UserCourseTestInterface $userCourseTest;
    private UserCourseRepository $userCourse;
    private ModuleQuestionInterface $moduleQuestion;
    private ModuleInterface $module;
    private CourseTestService $service;
    public function __construct(ModuleInterface $module, CourseTestInterface $courseTest, CourseTestService $service, UserCourseTestInterface $userCourseTest, ModuleQuestionInterface $moduleQuestion, CourseInterface $course, CourseTestQuestionInterface $courseTestQuestion, UserCourseRepository $userCourse)
    {
        $this->courseTest = $courseTest;
        $this->course = $course;
        $this->userCourseTest = $userCourseTest;
        $this->userCourse = $userCourse;
        $this->moduleQuestion = $moduleQuestion;
        $this->courseTestQuestion = $courseTestQuestion;
        $this->service = $service;
        $this->module = $module;
    }

    public function index(string $slug, Request $request): JsonResponse
    {
        try {
            $course = $this->course->showWithSlug($request, $slug);
            $courseTest = $this->courseTest->show($course->id);
            if ($courseTest == null) return ResponseHelper::error(null, "Anda Belum Setting Test");
            return ResponseHelper::success(CourseTestResource::make($courseTest), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * show
     *
     * @param  mixed $request
     * @param  mixed $courseTest
     * @return JsonResponse
     */
    public function show(Request $request, CourseTest $courseTest): JsonResponse
    {
        try {
            $this->service->store($courseTest);
            $request->merge(['course_id' => $courseTest->id]);
            $userCourseTests = $this->userCourseTest->customPaginate($request);
            $data['paginate'] = $this->customPaginate($userCourseTests->currentPage(), $userCourseTests->lastPage());
            $data['data'] = UserCourseTestResource::collection($userCourseTests);
            return responsehelper::success($data, trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
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
            $courseTests = $this->courseTest->get();
            return ResponseHelper::success(CourseTestResource::collection($courseTests), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method preTest
     *
     * @param CourseTest $courseTest [explicite description]
     *
     * @return JsonResponse
     */
    public function preTest(CourseTest $courseTest, Request $request): JsonResponse
    {
        try {
            $preTest = $this->service->preTest($courseTest);
            if ($preTest == 'anda sudah menyelesaikan pre-test ini') {
                return ResponseHelper::error(null, trans('alert.fetch_failed'));
            }
            $request->merge(['id' => $preTest['questions']]);
            $questions = $this->moduleQuestion->customPaginate($request);
            $data['paginate'] = $this->customPaginate($questions->currentPage(), $questions->lastPage());
            $data['data'] = ModuleQuestionStudentResource::collection($questions);
            $data['course_test'] = CourseTestResource::make($courseTest);
            $data['user_quiz'] = UserCourseTestResource::make($preTest['preTest']);
        
            // Add course data to the response
            $data['course'] = new CourseResource($courseTest->course);
        
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * postTest
     *
     * @param  mixed $request
     * @param  mixed $courseTest
     * @return JsonResponse
     */
    public function postTest(Request $request, CourseTest $course_test, UserCourseTest $user_course_test): JsonResponse
    {
        try {
            // $this->service->canAccessPostTest($course_test);

            $postTest = $this->service->postTest($course_test);
            if ($postTest == 'already') {
                return ResponseHelper::error(null, trans('alert.fetch_failed'));
            }
            $request->merge(['id' => $postTest['questions']]);
            $questions = $this->moduleQuestion->customPaginate($request);
            $data['paginate'] = $this->customPaginate($questions->currentPage(), $questions->lastPage());
            $data['data'] = ModuleQuestionStudentResource::collection($questions);
            $data['course_test'] = CourseTestResource::make($course_test);
            $data['user_quiz'] = UserCourseTestResource::make($postTest['postTest']);
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Throwable $e) {
            return ResponseHelper::error(null, trans('alert.not_pre_test_yet'). '. ' . $e->getMessage());
        } 
    }

    public function getPostTestId(CourseTest $courseTest): JsonResponse
    {
        try {
            $uct = UserCourseTest::query()
                ->where('user_id', auth()->id())
                ->where('course_test_id', $courseTest->id)
                ->where('test_type', TestEnum::POSTTEST->value)
                ->latest()
                ->first();
    
            if (! $uct) {
                throw new NotFoundHttpException("Post-test record tidak ditemukan.");
            }
    
            return ResponseHelper::success(['id' => $uct->id], trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * submit
     *
     * @param  mixed $request
     * @param  mixed $userCourseTest
     * @return JsonResponse
     */
    public function submit(UserCourseTestRequest $request, UserCourseTest $userCourseTest): JsonResponse
    {
        try {
            $this->service->submit($request, $userCourseTest);
            return ResponseHelper::success(null, trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }

    public function statistic(UserCourseTest $userCourseTest): JsonResponse
    {
        try {
            $result = $this->userCourseTest->show($userCourseTest->id);
    
            // Create the resource first
            $resource = new CourseTestResultResource($result);
    
            // Get the array data from the resource
            $data = $resource->toArray(request());
    
            // Make sure the course data is included with slug as fallback for title
            if ($result->courseTest && $result->courseTest->course) {
                $course = $result->courseTest->course;
                $data['course'] = [
                    'id' => $course->id,
                    'title' => $course->title ?? ucwords(str_replace('-', ' ', $course->slug)),
                    'slug' => $course->slug
                ];
            }
    
            // Return the response with the modified data
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function getTestIds(string $recordId): JsonResponse
    {
        try {
            $clickedRecord = UserCourseTest::with('courseTest')->findOrFail($recordId);
    
            // Cari kedua record pre & post untuk user + course yang sama
            $allTests = UserCourseTest::where('user_id', $clickedRecord->user_id)
                ->whereHas('courseTest', fn($q) =>
                    $q->where('course_id', $clickedRecord->courseTest->course_id)
                )
                ->get()
                ->keyBy('test_type');
    
            return ResponseHelper::success($allTests, trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * store
     *
     * @param  mixed $request
     * @param  mixed $slug
     * @return JsonResponse
     */
    public function store(CourseTestRequest $request, string $slug): mixed
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($slug);
            $data = $request->validated();
            $data['course_id'] = $course->id;
            $courseTest = $this->courseTest->store($data);
            $courseTest->courseTestQuestions()->delete();
    
            $result = [];
    
            foreach ($request['question_count'] as $index => $questionCount) {
                $module = $this->module->show($data['module_id'][$index]);
    
                if ($questionCount > $module->moduleQuestions()->count()) {
                    array_push($result, 'Jumlah pertanyaan pada modul ' . $module->title . ' tidak sama dengan yang Anda inputkan.');
                } else {
                    $storeData = [
                        'course_test_id' => $courseTest->id,
                        'question_count' => $questionCount,
                        'module_id' => $data['module_id'][$index]
                    ];
                    $this->courseTestQuestion->store($storeData);
                }
            }
    
            if (!empty($result)) {
                return ResponseHelper::error(null, $result);
            }
    
            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.accept_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method update
     *
     * @param CourseTestRequest $request [explicite description]
     * @param CourseTest $courseTest [explicite description]
     *
     * @return JsonResponse
     */
    public function update(CourseTestRequest $request, CourseTest $courseTest): JsonResponse
    {
        try {
            $this->courseTest->update($courseTest->id, $request->validated());
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method destroy
     *
     * @param CourseTest $courseTest [explicite description]
     *
     * @return JsonResponse
     */
    public function destroy(CourseTest $courseTest): JsonResponse
    {
        try {
            $this->courseTest->delete($courseTest->id);
            return ResponseHelper::success(true, trans('alert.delete_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.delete_failed') . '. ' . $th->getMessage());
        }
    }


    public function detailCourse(string $slug): JsonResponse
    {
        try {
            $courseTest = $this->courseTest->showWithSlug($slug);
            return ResponseHelper::success(CourseTestDetailResource::make($courseTest), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function resetTest($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $test = $this->userCourseTest->show($id);
                if (!$test) {
                    return ResponseHelper::error(trans('alert.data_not_found'), 404);
                }
    
                $userId   = $test->user_id;
                $courseId = optional($test->courseTest)->course_id;
    
                $test->delete();
    
                UserCourse::where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->update([
                        'has_pre_test'  => false,
                        'has_post_test' => false,
                    ]);
    
                return ResponseHelper::success(['deleted_id' => $id], trans('alert.delete_success'));
            });
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.delete_failed') . '. ' . $th->getMessage());
        }
    }
}
