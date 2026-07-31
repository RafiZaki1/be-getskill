<?php

namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\CourseTestInterface;
use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Contracts\Interfaces\Course\QuizInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseTest;
use App\Models\ModuleQuestion;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseTestRepository extends BaseRepository implements CourseTestInterface
{
    /**
     * Method __construct
     *
     * @param Quiz $Quiz [explicite description]
     *
     * @return void
     */
    public function __construct(CourseTest $courseTest)
    {
        $this->model = $courseTest;
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

    public function showWithSlug(string $slug): mixed
    {
        return $this->model->query()->whereHas('course', function($query) use ($slug){
            $query->where('slug', $slug);
        })->first();
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
        return $this->model->updateOrCreate(
            ['course_id' => $data['course_id']],
            $data
        );
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
        return $this->model->query()->where('course_id', $id)->first();
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
        return $this->model->query()->findOrFail($id)->update($data);
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
        return $this->model->query()->findOrFail($id)->delete();
    }
}
