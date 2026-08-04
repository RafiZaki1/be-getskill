<?php

namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\CourseTestInterface;
use App\Contracts\Interfaces\Course\CourseTestQuestionInterface;
use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Contracts\Interfaces\Course\QuizInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseTest;
use App\Models\CourseTestQuestion;
use App\Models\ModuleQuestion;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseTestQuestionRepository extends BaseRepository implements CourseTestQuestionInterface
{
    /**
     * Method __construct
     *
     * @param Quiz $Quiz [explicite description]
     *
     * @return void
     */
    public function __construct(CourseTestQuestion $courseTestQuestion)
    {
        $this->model = $courseTestQuestion;
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
        return $this->model->query()->where($data)->get();
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
    public function update(mixed $id, array $data): mixed
    {
        return $this->show($id)->update($data);
    }
    /**
     * Method delete
     *
     * @param mixed $id [explicite description]
     *
     * @return mixed
     */
    public function delete(mixed $id): mixed
    {
        return $this->show($id)->delete();
    }
}
