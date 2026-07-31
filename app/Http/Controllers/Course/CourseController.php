<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Contracts\Interfaces\Course\UserCourseInterface;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseUpdateRequest;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\Course\DetailCourseLandingPageResource;
use App\Http\Resources\Course\DetailCourseResource;
use App\Http\Resources\CourseListResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseStatisticResource;
use App\Http\Resources\CustomCourseResource;
use App\Http\Resources\QuizResource;
use App\Http\Resources\TopCourseResource;
use App\Http\Resources\UserCourseResource;
use App\Models\Course;
use App\Models\UserQuiz;
use App\Services\Course\CourseService;
use App\Traits\PaginationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class CourseController extends Controller
{
    use PaginationTrait;
    private CourseInterface $course;
    private UserCourseInterface $userCourse;
    private ModuleInterface $module;
    private CourseService $service;


    /**
     * Method __construct
     *
     * @param CourseInterface $course [explicite description]
     *
     * @return void
     */
    public function __construct(CourseInterface $course, CourseService $service, UserCourseInterface $userCourse, ModuleInterface $module)
    {
        $this->userCourse = $userCourse;
        $this->course = $course;
        $this->service = $service;
        $this->module = $module;
    }
    public function getSome(Request $request): JsonResponse
    {
        try {
            $course1 = $this->course->getSome($request);
            $course2 = $this->course->getSome2($request);

            $courses = $course1->merge($course2)
            ->filter(fn($course) => $course->is_ready); 

            return ResponseHelper::success(CustomCourseResource::collection($courses), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('get some course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }


    #[OA\Get(
        path: "/api/courses",
        operationId: "getAdminCourses",
        summary: "List semua kursus (Admin)",
        description: "Mendapatkan daftar semua kursus untuk keperluan admin",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Courses"]
    )]
    #[OA\Response(
        response: 200,
        description: "Berhasil mengambil data kursus",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object")),
                new OA\Property(property: "message", type: "string")
            ]
        )
    )]
    public function index(Request $request): JsonResponse
    {
        try{
            if ($request->has('page')) {
                $courses = $this->course->customPaginate($request);
                $data['paginate'] = $this->customPaginate($courses->currentPage(), $courses->lastPage());
                $data['data'] = CourseListResource::collection($courses);
            } else {
                $courses = $this->course->search($request);
                $data['data'] = CourseListResource::collection($courses);
            }
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('index course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Post(
        path: "/api/courses",
        operationId: "storeAdminCourse",
        summary: "Buat kursus baru (Admin)",
        description: "Menyimpan data kursus baru",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Courses"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "title", type: "string", example: "Kursus Laravel Dasar"),
                new OA\Property(property: "description", type: "string", example: "Deskripsi kursus"),
                new OA\Property(property: "price", type: "integer", example: 100000)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Kursus berhasil ditambahkan")]
    public function store(StoreCourseRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->course->store($this->service->store($request));
            DB::commit();
            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('store course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Get(
        path: "/api/courses/{slug}",
        operationId: "showAdminCourse",
        summary: "Detail kursus (Admin)",
        description: "Melihat detail kursus berdasarkan slug",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Courses"]
    )]
    #[OA\Parameter(name: "slug", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Detail kursus berhasil diambil")]
    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $course = $this->course->showWithSlug($request, $slug);
            return ResponseHelper::success(DetailCourseResource::make($course), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('show course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function showLanding(Request $request, string $slug): JsonResponse
    {
        try {
            $course = $this->course->showWithSlug($request, $slug);
            return ResponseHelper::success(DetailCourseLandingPageResource::make($course), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('show landing course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Put(
        path: "/api/courses/{course}",
        operationId: "updateAdminCourse",
        summary: "Update kursus (Admin)",
        description: "Mengubah data kursus yang ada",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Courses"]
    )]
    #[OA\Parameter(name: "course", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "title", type: "string", example: "Kursus Laravel Update"),
                new OA\Property(property: "description", type: "string", example: "Deskripsi update"),
                new OA\Property(property: "price", type: "integer", example: 120000)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Kursus berhasil diupdate")]
    public function update(CourseUpdateRequest $request, Course $course): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->course->update($course->id, $this->service->update($course, $request));
            DB::commit();
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('update course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Delete(
        path: "/api/courses/{course}",
        operationId: "deleteAdminCourse",
        summary: "Hapus kursus (Admin)",
        description: "Menghapus data kursus",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Courses"]
    )]
    #[OA\Parameter(name: "course", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Kursus berhasil dihapus")]
    public function destroy(Course $course): JsonResponse
    {
        try {
            $this->course->delete($course->id);
            return ResponseHelper::success(true, trans('alert.delete_success'));
        } catch (\Throwable $e) {
            return ResponseHelper::error(true, trans('alert.delete_constrained'));
        }
    }
    public function statistic(Request $request, string $slug): JsonResponse
    {
        try {
            $course = $this->course->showWithSlug($request, $slug,);
            // dd($course->transactions);
            return ResponseHelper::success(CourseStatisticResource::make($course), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('statistic course  error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    public function topCourses(): JsonResponse
    {
        try {
            $courses = $this->course->getTop();
            return ResponseHelper::success(TopCourseResource::collection($courses), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('top course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    public function topRatings(): JsonResponse
    {
        try {
            $courses = $this->course->topRatings();
            return ResponseHelper::success(TopCourseResource::collection($courses), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('top ratings course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method readyToUse
     *
     * @param Course $course [explicite description]
     *
     * @return JsonResponse
     */
    public function readyToUse(Course $course): JsonResponse
    {
        try {
            $completeSetting = $this->service->publish($course);
            if ($completeSetting['modules'] == 0 || $completeSetting['sub_modules'] == 0 || !$completeSetting['test']) {
                return ResponseHelper::error(null, "Belum ada sub modul pada kursus ini", Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $course = $this->course->update($course->id, ['is_ready' => true]);

            return ResponseHelper::success($course, trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            Log::error('ready to use course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    /*
     * Method makeDraft
     *
     * @param Course $course 
     *
     * @return JsonResponse
     */
    public function makeDraft(Course $course): JsonResponse
    {
        try {
            $course = $this->course->update($course->id, ['is_ready' => false]);
            return ResponseHelper::success($course, trans('alert.update_success'));
        } catch (\Throwable $th) {
            Log::error('make draft course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method listCourse
     *
     * @param Request $request [explicite description]
     *
     * @return JsonResponse
     */
    public function listCourse(Request $request): JsonResponse
    {
        try{
            $request->merge(['is_ready' => true]);
            if ($request->has('page')) {
                $courses = $this->course->customPaginate($request);
                $data['paginate'] = $this->customPaginate($courses->currentPage(), $courses->lastPage());
                $data['data'] = CourseResource::collection($courses);
            } else {
                $courses = $this->course->search($request);
                $data['data'] = CourseResource::collection($courses);
            }
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('list course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method share
     *
     * @param string $slug [explicite description]
     *
     * @return JsonResponse
     */
    public function share(Request $request, string $slug): JsonResponse
    {
        try{
            $course = $this->course->showWithSlug($request, $slug);
            return ResponseHelper::success(CourseResource::make($course), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('share course error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function count(): JsonResponse
    {
        try{
            $course_count = $this->course->count();
            return ResponseHelper::success(['course_count' => $course_count], trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function getBySubModule($subModule)
    {
        try{
            $data = $this->course->getBySubModuleSlug($subModule);
            // return ResponseHelper::success($subModule);
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * checkSubmit
     *
     * @return JsonResponse
     */
    public function checkSubmit(UserQuiz $userQuiz): JsonResponse
    {
        try{
        $quiz = $userQuiz->quiz->minimum_score;
        if ($userQuiz->score >= $quiz) {
            $userCourse = $this->userCourse->showByCourse($userQuiz->quiz->module->course_id);
            $module_step = 1;
            foreach ($userCourse->course->modules as $module) {
                if ($module->step > $module_step) {
                    $module_step = $module->step;
                }
            }
            $module = $this->module->whereStepCourse($module_step, $userCourse->course->id);
            if ($userQuiz->quiz->module->id == $module->id) {
                $data['status'] = 'finished';
                $data['parameter'] = $userQuiz->quiz->module->slug;
            } else {
                $data['status'] = 'not_finished';
                $data['parameter'] = $userQuiz->quiz->module->slug;
            }
        } else {
            $data['status'] = 'not_finished';
            $data['parameter'] = $userQuiz->quiz->module->slug;
        }
        $data['course'] = CourseResource::make($userQuiz->quiz->module->course);
        return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Arrange course's modules by step
     *
     * @param Course $course
     */
    public function arrangeModules(Course $course)
    {
        DB::beginTransaction();
        try {

            $this->service->arrangeModuleSteps($course);

            DB::commit();
            return ResponseHelper::success(null, trans('alert.update_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(null, trans('alert.update_failed'));
        }
    }
}
