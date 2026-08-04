<?php

namespace App\Contracts\Repositories;

use App\Models\Blog;
use App\Models\User;
use App\Models\Event;
use App\Models\UserQuiz;
use App\Enums\UserRoleEnum;
use App\Helpers\UserHelper;
use App\Models\EventDetail;
use Illuminate\Http\Request;
use App\Models\UserCourseTest;
use Illuminate\Support\Facades\DB;
use App\Traits\Datatables\UserDatatable;
use App\Contracts\Interfaces\BlogInterface;
use App\Contracts\Interfaces\UserInterface;
use App\Contracts\Interfaces\EventInterface;
use App\Contracts\Interfaces\RegisterInterface;
use App\Contracts\Interfaces\UserQuizInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\Interfaces\EventDetailInterface;
use App\Contracts\Interfaces\UserCourseTestInterface;
use App\Enums\TestEnum;

class UserCourseTestRepository extends BaseRepository implements UserCourseTestInterface
{
    public function __construct(UserCourseTest $userCourseTest)
    {
        $this->model = $userCourseTest;
    }
    /**
     * Method get
     *
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->model->query()->get();
    }

    /**
     * customPaginate
     *
     * @param  mixed $request
     * @param  mixed $pagination
     * @return LengthAwarePaginator
     */
    public function customPaginate(Request $request, int $pagination = 10): LengthAwarePaginator
    {
        $baseQuery = $this->model->newQuery()
            ->join('users', 'users.id', '=', 'user_course_tests.user_id')
            ->join('course_tests', 'course_tests.id', '=', 'user_course_tests.course_test_id')
            ->when($request->course_id, fn($query) => $query->where('course_tests.course_id', $request->course_id))
            ->when($request->name, fn($query) =>
                $query->where('users.name', 'like', '%' . $request->name . '%')
            )
            ->select([
                'users.id as user_id',
                'users.name as user_name',

                DB::raw("MAX(CASE WHEN user_course_tests.test_type = 'pre-test'  THEN user_course_tests.score END)  as pre_score"),
                DB::raw("MAX(CASE WHEN user_course_tests.test_type = 'post-test' THEN user_course_tests.score END)  as post_score"),

                DB::raw("
                    COALESCE(
                        MAX(CASE WHEN user_course_tests.test_type = 'pre-test'  THEN user_course_tests.id END),
                        MAX(CASE WHEN user_course_tests.test_type = 'post-test' THEN user_course_tests.id END)
                    ) as record_id
                "),
            ])
            ->groupBy('users.id', 'users.name');

        return $baseQuery->paginate($pagination);
    }

    /**
     * Method store
     *
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->model->query()->create($data);
    }
    /**
     * Method show
     *
     * @param mixed $id [explicite description]
     *
     * @return mixed
     */
    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }
    /**
     * Method update
     *
     * @param mixed $id [explicite description]
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function update(mixed $id, array $data): mixed
    {
        return $this->model->query()->findOrFail($id)->update($data);
    }

    public function getByClassroom(mixed $data): mixed
    {
        return $this->model->query()
            ->whereNotNull('score')
            ->whereHas('user', function ($query) use ($data) {
                $query->whereHas('student', function ($query) use ($data) {
                    $query->whereHas('studentClassrooms', function ($query) use ($data) {
                        $query->where('classroom_id', $data);
                    });
                });
            });
    }

    public function getStatsByClassroom(string $classroom): mixed
    {
        return $this->model->query()
            ->whereNotNull('score')
            ->whereHas('user', function ($query) use ($classroom) {
                $query->whereHas('student', function ($query) use ($classroom) {
                    $query->whereHas('studentClassrooms', function ($query) use ($classroom) {
                        $query->where('classroom_id', $classroom);
                    });
                });
            })->selectRaw("
                COUNT(*) as count_student,
                MAX(score) as highest,
                MIN(score) as lowest,
                AVG(score) as average
            ")
            ->first();
    }

    /**
     * Remove score for a given UserCourseTest entry
     *
     * @param mixed $userCourseTestId
     * @return mixed
     */
    public function removeScore(mixed $userCourseTestId): mixed
    {
        $userCourseTest = $this->model->query()->findOrFail($userCourseTestId);

        // Set the score to null
        $userCourseTest->score = null;
        $userCourseTest->save();

        return $userCourseTest;
    }

    /**
     * get user course by classroom and course slug with filter and paginate
     *
     * @param Request $request
     * @param string $classroom_id
     * @param string $slug
     * @param integer $paginate
     * @return LengthAwarePaginator
     */
    public function getByClassroomAndCourse(Request $request, string $classroom_id, string $slug, int $paginate = 10): LengthAwarePaginator
    {
        return $this->model->query()
            ->with(['user.student.studentClassrooms.classroom' => function($query) use ($classroom_id) {
                $query->where('id', $classroom_id);
            }])
            ->whereNotNull('score')
            ->whereRelation('courseTest.course', 'slug', $slug)
            ->whereRelation('user.student.studentClassrooms', 'classroom_id', $classroom_id)
            ->when($request->search, function($query) use ($request) {
                $query->whereRelation('user', 'name', 'like', "%$request->search%");
            })
            ->when($request->type, function($query) use ($request) {
                $query->where('test_type', $request->type);
            })
            ->fastPaginate($paginate);
    }
}
