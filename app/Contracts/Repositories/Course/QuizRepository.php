<?php

namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Contracts\Interfaces\Course\QuizInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\Category;
use App\Models\Course;
use App\Models\ModuleQuestion;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class QuizRepository extends BaseRepository implements QuizInterface
{
    /**
     * Method __construct
     *
     * @param Quiz $Quiz [explicite description]
     *
     * @return void
     */
    public function __construct(Quiz $quiz)
    {
        $this->model = $quiz;
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
     * Method store
     *
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->model->updateOrCreate(
            ['module_id' => $data['module_id']],
            $data
        );
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
     * Method show
     *
     * @param mixed $id [explicite description]
     *
     * @return mixed
     */
    public function show(mixed $id): mixed
    {
        return $this->model->query()->where('module_id', $id)->firstOrFail();
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

    
    public function getByCourse(string $slug): mixed
    {
        return $this->model->query()
            ->whereHas('module', function ($query) use ($slug) {
                $query->whereRelation('course', 'slug', $slug);
            })
            ->get();
    }
}
