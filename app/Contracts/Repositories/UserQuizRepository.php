<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\BlogInterface;
use App\Contracts\Interfaces\EventDetailInterface;
use App\Contracts\Interfaces\EventInterface;
use App\Contracts\Interfaces\RegisterInterface;
use App\Contracts\Interfaces\UserInterface;
use App\Contracts\Interfaces\UserQuizInterface;
use App\Enums\UserRoleEnum;
use App\Helpers\UserHelper;
use App\Models\Blog;
use App\Models\Event;
use App\Models\EventDetail;
use App\Models\User;
use App\Models\UserQuiz;
use App\Traits\Datatables\UserDatatable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserQuizRepository extends BaseRepository implements UserQuizInterface
{
    public function __construct(UserQuiz $userQuiz)
    {
        $this->model = $userQuiz;
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
     * Method getWhere
     *
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function getWhere(array $data): mixed
    {
        $dataQuery = $this->model->query()
            ->where('user_id', auth()->user()->id)
            ->whereNotNull('score')
            ->with('quiz')
            ->whereRelation('quiz', 'module_id', $data['module_id'])
            ->get();

        return $dataQuery;
    }

    /**
     * getByUserAndCourse
     *
     * @param  mixed $user
     * @param  mixed $courseSlug
     * @return mixed
     */
    public function getByUserAndCourseCompleted(User $user, string $courseSlug): mixed
    {
        return $this->model->query()
            ->where('user_id', $user->id)
            ->whereHas('quiz.module.course', function ($query) use ($courseSlug) {
                $query->where('slug', $courseSlug);
            })
            ->whereHas('quiz', function ($query) {
                $query->whereColumn(
                    'user_quizzes.score',
                    '>=',
                    'quizzes.minimum_score'
                );
            })
            ->with('quiz')
            ->get();
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

    /**
     * Method customPaginate
     *
     * @param Request $request [explicite description]
     * @param int $pagination [explicite description]
     *
     * @return LengthAwarePaginator
     */
    public function customPaginate(Request $request, int $pagination = 1): LengthAwarePaginator
    {
        return $this->model->query()->fastPaginate($pagination);
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
     * show
     *
     * @param  mixed $id
     * @return mixed
     */
    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }
}
