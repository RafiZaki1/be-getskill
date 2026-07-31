<?php

namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\Category;
use App\Models\Course;
use App\Models\ModuleQuestion;
use Hammerstone\paginate\paginate;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ModuleQuestionRepository extends BaseRepository implements ModuleQuestionInterface
{
    /**
     * Method __construct
     *
     * @param ModuleQuestion $ModuleQuestion [explicite description]
     *
     * @return void
     */
    public function __construct(ModuleQuestion $moduleQuestion)
    {
        $this->model = $moduleQuestion;
    }
    public function customPaginate(Request $request, int $pagination = 1): LengthAwarePaginator
    {
        $questionIds = implode("', '", $request->id);
        return $this->model
            ->query()
            ->whereIn('id', $request->id)
            ->orderByRaw("FIELD(id, '$questionIds')")
            ->paginate($pagination);
    }
    /**
     * Method getQuestions
     *
     * @param mixed $id [explicite description]
     * @param mixed $total [explicite description]
     *
     * @return mixed
     */
    public function getQuestions(mixed $id, mixed $total): mixed
    {
        return $this->model
            ->query()
            ->where(function ($query) use ($id) {
                $query->whereHas('module', function ($query) use ($id) {
                    $query->where('course_id', $id);
                })
                    ->orWhere('module_id', $id);
            })
            ->inRandomOrder()
            ->limit($total)
            ->get();
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
     * getByModule
     *
     * @param  mixed $id
     * @return mixed
     */
    public function getByModule(string $id): mixed
    {
        return $this->model->query()->where('module_id', $id)->get();
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

    /**
     * get where in
     *
     * @param mixed $ids
     * @return Collection
     */
    public function whereIn(mixed $ids): Collection
    {
        return $this->model->query()
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(callback: fn($question) => array_search($question->id, $ids))
            ->values();
    }
} 
