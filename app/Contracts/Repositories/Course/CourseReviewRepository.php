<?php

namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\CourseReviewInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\Category;
use App\Models\CourseReview;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseReviewRepository extends BaseRepository implements CourseReviewInterface
{
    public function __construct(CourseReview $courseReview)
    {
        $this->model = $courseReview;
    }
    /**
     * Method get
     *
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->model->get();
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
        return $this->model->findOrFail($id)->update($data);
    }

    public function latest(int $limit): mixed
    {
        return $this->model->query()->latest()->with(['course.subCategory.category', 'user'])->limit($limit)->get();
    }    
    /**
     * Method getLatest
     *
     * @return mixed
     */
    public function getLatest(): mixed
    {
        return $this->model->query()->latest()->limit(5)->get();
    }

    public function delete(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id)->delete();
    }
}
