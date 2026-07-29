<?php

namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\UserCourseInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\Category;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserCourseRepository extends BaseRepository implements UserCourseInterface
{
    public function __construct(UserCourse $userCourse)
    {
        $this->model = $userCourse;
    }


    /**
     * Method customPaginate
     *
     * @param Request $request [explicite description]
     * @param int $pagination [explicite description]
     *
     * @return LengthAwarePaginator
     */
    public function customPaginate(Request $request, int $pagination = 10): LengthAwarePaginator
    {
        return $this->model->query()
            ->with([
                'subModule.module',
                'course' => function ($query) {
                    $query->with(['subCategory', 'user'])
                        ->withCount('userCourses')
                        ->withAvg('courseReviews', 'rating')
                        ->with([
                            'modules' => function ($query) {
                                $query->select('id', 'course_id', 'step')
                                    ->withCount('subModules')
                                    ->with('quizzes', function ($query) {
                                        $query->with('userQuizzes', function ($query) {
                                            $query->where('user_id', auth()->user()->id)->exists();
                                        });
                                    });
                            }
                        ]);
                }
            ])
            ->when($request->user_id, function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->name, function ($query) use ($request) {
                $keyword = $request->name;
                return $query->whereHas('course', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                });
            })
            ->fastPaginate($pagination);
    }

    public function courseActivity(Request $request, int $pagination = 10): LengthAwarePaginator
    {
        return $this->model->query()
            ->with([
                'subModule.module',
                'course' => function ($courseQuery) use ($request) {
                    $courseQuery->with(['subCategory', 'user'])
                        ->withCount('userCourses')
                        ->withAvg('courseReviews', 'rating')
                        ->with([
                            'modules' => function ($moduleQuery) use ($request) {
                                $moduleQuery->select('id', 'course_id', 'step')
                                    ->withCount('subModules')
                                    ->with(['quizzes' => function ($quizQuery) use ($request) {
                                        $quizQuery->with(['userQuizzes' => function ($uqQuery) use ($request) {
                                            if ($request->filled('user_id')) {
                                                $uqQuery->where('user_id', $request->input('user_id'));
                                            }
                                        }]);
                                    }]);
                            }
                        ]);
                }
            ])
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->input('user_id')))
            ->unless($request->filled('user_id'), fn($q) => auth()->check() ? $q->where('user_id', auth()->id()) : $q)
            ->when($request->filled('name'), function ($q) use ($request) {
                $keyword = $request->input('name');
                $q->whereHas('course', fn($cq) => $cq->where('title', 'like', "%{$keyword}%"));
            })
            ->fastPaginate($pagination);
    }

    public function findByCourseAndUser($courseId, $userId): mixed
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)->first();
    }

    /**
     * store
     *
     * @param  mixed $data
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->model->query()->create($data);
    }

    /**
     * customUpdate
     *
     * @param  mixed $courseId
     * @param  mixed $data
     * @return mixed
     */
    public function customUpdate(mixed $courseId, array $data): mixed
    {
        return $this->showByCourse($courseId)->update($data);
    }
    /**
     * delete
     *
     * @param  mixed $id
     * @return mixed
     */
    public function delete(mixed $id): mixed
    {
        return $this->show($id)->delete();
    }
    /**
     * show
     *
     * @param  mixed $id
     * @return mixed
     */
    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    /**
     * update
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return mixed
     */
    public function update(mixed $id, array $data): mixed
    {
        return $this->showByCourse($id)->update($data);
    }
    /**
     * Method showByCourse
     *
     * @param $courseId $courseId [explicite description]
     *
     * @return mixed
     */
    public function showByCourse($courseId): mixed
    {
        return $this->model->query()->where('user_id', auth()->user()->id)->where('course_id', $courseId)->with('subModule')->firstOrFail();

    }
    public function checkByCourse($courseId, $user_id = null): mixed
    {
        return $this->model->query()->where('user_id', $user_id ?? auth()->user()->id)->where('course_id', $courseId)->with('subModule')->first();
    }

    /**
     * Method to remove has_pre_test field
     *
     * @param  mixed $courseId
     * @return mixed
     */
    public function removeHasPreTest(mixed $courseId): mixed
    {
        $userCourse = $this->model->query()->where('course_id', $courseId)->where('user_id', auth()->user()->id)->first();

        if (!$userCourse) {
            return null; // Or throw an exception if needed
        }

        // Remove the has_pre_test field
        $userCourse->has_pre_test = null;
        $userCourse->save();

        return $userCourse;
    }
}
