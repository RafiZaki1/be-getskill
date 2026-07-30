<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\CourseReviewInterface;
use App\Contracts\Interfaces\Course\UserCourseInterface;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseReviewRequest;
use App\Http\Resources\CourseReviewResource;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserCourseTest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseReviewController extends Controller
{
    private CourseReviewInterface $courseReview;
    private UserCourseInterface $userCourse;
    /**
     * Method __construct
     *
     * @param CourseReviewInterface $courseReview [explicite description]
     *
     * @return void
     */
    public function __construct(CourseReviewInterface $courseReview, UserCourseInterface $userCourse)
    {
        $this->courseReview = $courseReview;
        $this->userCourse = $userCourse;
    }
    /**
     * Method index
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $courseReview = $this->courseReview->get();
            return ResponseHelper::success(CourseReviewResource::collection($courseReview), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method getLatest
     *
     * @return JsonResponse
     */
    public function getLatest(): JsonResponse
    {
        try {
            $courseReviews = $this->courseReview->getLatest();
            return ResponseHelper::success(CourseReviewResource::collection($courseReviews), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method store
     *
     * @param CourseReviewRequest $request [explicite description]
     * @param Course $course [explicite description]
     *
     * @return mixed
     */
    public function store(CourseReviewRequest $request, Course $course): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->user()->id;
            $data['course_id'] = $course->id;
            $userCourse = $this->userCourse->showByCourse($course->id);
            $updated_at = Carbon::make($userCourse->updated_at);
            if ($userCourse->has_post_test && $updated_at->diffInMonths(Carbon::now())) {
                return ResponseHelper::error(false, trans('alert.review_expired'));
            }
    
            // pengecekan apakah user sudah benar benar menyelesaikan post test
            try {
                UserCourse::where(['user_id' => auth()->user()->id, 'course_id' => $course->id, 'has_post_test' => true])->firstOrFail();
            } catch (\Throwable $e) {
                return ResponseHelper::error(null, "anda belum menyelesaikan kursus");
            }
    
            // pengecekan apakah user sudah pernah mereview kursus, jika sudah maka tidak akan bisa review lebih dari 1
            $reviewAlready = CourseReview::where(['user_id' => auth()->user()->id, 'course_id' => $course->id])->exists();
            if (!$reviewAlready) {
                $this->courseReview->store($data);
                return ResponseHelper::success(true, trans('alert.add_success'));
            } else {
                return ResponseHelper::error(null, trans('Anda sudah memberi rating pada kursus ini'));
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method show
     *
     * @param CourseReview $courseReview [explicite description]
     *
     * @return JsonResponse
     */
    public function show(CourseReview $courseReview): JsonResponse
    {
        try {
            $courseReview = $this->courseReview($courseReview->id);
            return ResponseHelper::success(new CourseReviewResource($courseReview), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method update
     *
     * @param CourseReviewRequest $request [explicite description]
     * @param CourseReview $courseReview [explicite description]
     *
     * @return JsonResponse
     */
    public function update(CourseReviewRequest $request, CourseReview $courseReview): JsonResponse
    {
        try {
            $this->courseReview->update($courseReview->id, $request->validated());
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    public function latest(): JsonResponse
    {
        try {
            $courseReview = $this->courseReview->latest(3);
            return ResponseHelper::success($courseReview, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function destroy(CourseReview $courseReview) 
    {
        try {
            $this->courseReview->delete($courseReview->id);
            return ResponseHelper::success($courseReview, trans('alert.delete_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.delete_failed') . '. ' . $th->getMessage());
        }
    }
}
