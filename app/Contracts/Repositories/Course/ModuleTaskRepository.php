<?php

namespace App\Contracts\Repositories\Course;

use App\Models\Course;
use App\Models\Category;
use App\Models\ModuleTask;
use Illuminate\Http\Request;
use App\Contracts\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\ModuleTaskInterface;

class ModuleTaskRepository extends BaseRepository implements ModuleTaskInterface
{
    /**
     * Method __construct
     *
     * @param ModuleTask $moduleTask [explicite description]
     *
     * @return void
     */
    public function __construct(ModuleTask $moduleTask)
    {
        $this->model = $moduleTask;
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
        return $this->model->query()->where($data)->orderBy('created_at', 'asc')->get();
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

    public function getByCourse(string $slug)
    {
        return $this->model->query()->whereRelation('module.course', 'slug', $slug)->get();
    }

    public function getByCourseWithPaginate(Request $request, string $slug, int $pagination = 10, ): LengthAwarePaginator
    {
        return $this->model->query()->whereRelation('module.course', 'slug', $slug)
        ->when($request->module, function($query) use ($request) {
            $query->where('module_id', $request->module);
        })->when($request->search, function ($query) use ($request) {
            $query->where('question', 'like', '%' . $request->search . '%');
        })
        ->fastPaginate($pagination);
    }

    public function searchUser(Request $request)
    {
        $name = $request->get('name');

        return ModuleTask::with([
            'submissionTask' => function ($q) use ($name) {
                $q->when($name, function ($q2) use ($name) {
                    $q2->whereHas('user', function ($q3) use ($name) {
                        $q3->where('name', 'like', '%' . $name . '%');
                    });
                })->with('user');
            },
            'module'
        ]);
    }
}
