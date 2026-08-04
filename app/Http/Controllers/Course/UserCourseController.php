<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\UserCourseInterface;
use App\Helpers\CourcePercentaceHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCourseResource;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Module;
use App\Models\SubModule;
use App\Models\User;
use App\Models\UserCourse;
use App\Services\UserCourseService;
use App\Traits\PaginationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class UserCourseController extends Controller
{
    use PaginationTrait;

    private UserCourseInterface $userCourse;
    private CourseInterface $course;
    private UserCourseService $service;

    public function __construct(UserCourseInterface $userCourse, UserCourseService $service, CourseInterface $course)
    {
        $this->userCourse = $userCourse;
        $this->course = $course;
        $this->service = $service;
    }

    /**
     * index
     *
     * @param  mixed $request
     * @param  mixed $course
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userCourses = $this->userCourse->customPaginate($request);
            $data['paginate'] = $this->customPaginate($userCourses->currentPage(), $userCourses->lastPage());
            $data['data'] = UserCourseResource::collection($userCourses);
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * guest
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function guest(Request $request): JsonResponse
    {
        try {
            $request->merge(['user_id' => auth()->user()->id]);
            $userCourses = $this->userCourse->customPaginate($request);
            $data['paginate'] = $this->customPaginate($userCourses->currentPage(), $userCourses->lastPage());
            $data['data'] = UserCourseResource::collection($userCourses);
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function userCourseActivity(Request $request): JsonResponse
    {
        try {
            if ($request->has('user_id')) {
                $request->merge(['user_id' => $request->input('user_id')]);
            } elseif (auth()->check()) {
                $request->merge(['user_id' => auth()->id()]);
            }
    
            $userCourses = $this->userCourse->courseActivity($request);
            $data['paginate'] = $this->customPaginate(
                $userCourses->currentPage(),
                $userCourses->lastPage()
            );
            $data['data'] = UserCourseResource::collection($userCourses);
    
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * getByUser
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return JsonResponse
     */
    public function getByUser(Request $request, User $user): JsonResponse
    {
        try {
            $request->merge(['user_id' => $user->id]);
            $userCourses = $this->userCourse->customPaginate($request);
            $data['paginate'] = $this->customPaginate($userCourses->currentPage(), $userCourses->lastPage());
            $data['data'] = UserCourseResource::collection($userCourses);
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * updateLastStepUser
     *
     * @return JsonResponse
     */
    public function userLastStep(string $slug, SubModule $subModule): JsonResponse
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($slug);
            $userCourse = UserCourseResource::make($this->userCourse->showByCourse($course->id));
            $userCourse->course->test_id = $course->courseTest?->id;
            
            $this->service->userLastStep($course, $subModule);
            return ResponseHelper::success($userCourse, 'Berhasil masuk materi');
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function checkPayment(Request $request)
    {
        $userCourse = UserCourse::with('subModule')->where('user_id', auth()->user()->id)->whereRelation('course', 'slug', $request->course_slug)->first();
        if ($userCourse) {
            return ResponseHelper::success(['user_course' => UserCourseResource::make($userCourse)]);
        } else {
            return ResponseHelper::error(['user_course' => $userCourse, 'course' => Course::with(['modules.subModules'])->where('slug', $request->course_slug)->first()]);
        }
    }

    public function store(Request $request, string $slug): mixed
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($slug);
            $userCourse = $this->userCourse->checkByCourse($course->id) ?? $this->userCourse->store([
                'course_id' => $course->id,
                'user_id' => auth()->user()->id,
                'sub_module_id' => $course->modules()->orderBy('step', 'asc')->first()->subModules()->orderBy('step', 'asc')->first()->id
            ]);
            return ResponseHelper::success(UserCourseResource::make($userCourse), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage());
        }
    }
    /**
     * getuserStats
     *
     * @return JsonResponse
     */
    public function getUserStats(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $this->service->userStats($user->id);

        return ResponseHelper::success($data, trans('alert.fetch_success'));
    }

    /**
     * deleteHasPreTest
     *
     * @param  mixed $request
     * @param  string $slug
     * @return JsonResponse
     */
    /**
 * Remove has_pre_test field for the user in the given course
 *
 * @param  mixed $request
 * @param  string $slug
 * @return JsonResponse
 */
    public function removePreTest(Request $request, string $slug): JsonResponse
    {
        try {
            // Retrieve the course by slug
            $course = $this->course->showWithSlugWithoutRequest($slug);

            // Call the service method to remove the has_pre_test field
            $userCourse = $this->service->removeHasPreTestField($course->id);

            if (!$userCourse) {
                return ResponseHelper::error('User course not found or already removed');
            }

            return ResponseHelper::success(null, 'Successfully removed pre-test information');
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage());
        }
    }

    /**
     * check if user has access to course
     * 
     * @param Course $course
     * @return JsonResponse
     */
    public function hasCourseAccess(Course $course): JsonResponse
    {
        try {
            $existsCourse = $this->userCourse->showByCourse($course->id);
    
            if ($existsCourse) {
                $existsCourse->sub_module_slug = $existsCourse->subModule->slug;
                $existsCourse->study_percentage = CourcePercentaceHelper::getPercentace($existsCourse);
                return ResponseHelper::success($existsCourse, trans('alert.fetch_success'));
            } else {
                return ResponseHelper::error(null, trans('alert.fetch_failed'), 404);
            }
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
}
