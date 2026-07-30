<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\UserCourseTestInterface;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseTestResultResource;
use App\Http\Resources\TestDetailResource;
use App\Http\Resources\TestHistoryResource;
use App\Http\Resources\UserCourseResource;
use App\Http\Resources\UserCourseTestResource;
use App\Services\UserCourseTestService;
use App\Traits\PaginationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserCourseTestController extends Controller
{
    use PaginationTrait;
    private UserCourseTestInterface $userCourseTest;
    private UserCourseTestService $service;
    private CourseInterface $course;
    public function __construct(UserCourseTestInterface $userCourseTest, CourseInterface $course, UserCourseTestService $service)
    {
        $this->userCourseTest = $userCourseTest;
        $this->service = $service;
        $this->course = $course;
    }
    /**
     * Method index
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userCourseTests = $this->userCourseTest->customPaginate($request);
        $data['paginate'] = $this->customPaginate($userCourseTests->currentPage(), $userCourseTests->lastPage());
        $data['data'] = TestHistoryResource::collection($userCourseTests);
        return ResponseHelper::success($data, trans('alert.fetch_success'));
    }
    public function getByCourse(Request $request, string $slug): JsonResponse
    {
        $course = $this->course->showWithSlug($request, $slug);
        $request->merge(['course_id' => $course->id]);
        $userCourseTests = $this->userCourseTest->customPaginate($request);
        $data['paginate'] = $this->customPaginate($userCourseTests->currentPage(), $userCourseTests->lastPage());
        $data['data'] = TestHistoryResource::collection($userCourseTests);
        return ResponseHelper::success($data, trans('alert.fetch_success'));
    }

    /**
     * Method store
     *
     * @param Request $request [explicite description]
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->userCourseTest->store($request->validated());
        return ResponseHelper::success(true, trans('alert.add_success'));
    }


}
