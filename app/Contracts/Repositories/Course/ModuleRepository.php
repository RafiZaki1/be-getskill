<?php
namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ModuleRepository extends BaseRepository implements ModuleInterface
{

    /**
     * Method __construct
     *
     * @param Module $module [explicite description]
     *
     * @return void
     */
    public function __construct(Module $module)
    {
        $this->model = $module;
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
        return $this->model->query()->with('subModules')
            ->when($request->search, function ($query) use ($request) {
                $query->whereLike('title', $request->search);
            })->when($request->course_id, function ($query) use ($request) {
            $query->where('course_id', $request->course_id);
        })
            ->fastPaginate($pagination);
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
        return $this->model->query()->with(['moduleTasks' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);
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
     * getOneByCourse
     *
     * @param  mixed $data
     * @return mixed
     */
    public function getOneByCourse(string $id): mixed
    {
        return $this->model->query()
            ->where('course_id', $id)
            ->latest()
            ->first();
    }

    public function shiftStepsUpward(int $fromStep, string $courseId, string $excludeId): void
    {
        $this->model
            ->where('course_id', $courseId)
            ->where('step', '>=', $fromStep)
            ->where('id', '!=', $excludeId)
            ->orderByDesc('step')
            ->get()
            ->each(function ($module) {
                $module->increment('step');
            });
    }

    public function normalizeSteps(string $courseId): void
    {
        $this->model
            ->where('course_id', $courseId)
            ->orderBy('step')
            ->get()
            ->values()
            ->each(function ($module, $index) {
                $module->update(['step' => $index + 1]);
            });
    }

    /**
     * Method getOneStepForward
     *
     * @param mixed $step [explicite description]
     *
     * @return mixed
     */
    public function getForward(mixed $step, string $id): mixed
    {
        return $this->model
            ->query()
            ->where('course_id', $id)
            ->where('step', '>', $step)
            ->orderBy('step', 'ASC')
            ->first();
    }
    /**
     * Method getOneStepBackward
     *
     * @param mixed $step [explicite description]
     *
     * @return mixed
     */
    public function getBackward(mixed $step, string $id): mixed
    {
        return $this->model
            ->query()
            ->where('course_id', $id)
            ->where('step', '<', $step)
            ->orderByDesc('step')
            ->first();
    }
    /**
     * Method getWhere
     *
     * @param string $column [explicite description]
     * @param string $operator [explicite description]
     * @param $value $value [explicite description]
     *
     * @return mixed
     */
    public function getWhere(string $column, string $operator, $value): mixed
    {
        return $this->model->query()->where($column, $operator, $value)->get();
    }

    /**
     * search
     *
     * @param  mixed $request
     * @return mixed
     */
    public function search(Request $request): mixed
    {
        return $this->model->query()
            ->with([
                'course',
                'quizzes',
                'moduleQuestions',
                'subModules',
                'moduleTasks',
            ])
            ->withCount([
                'quizzes',
                'moduleQuestions',
                'subModules',
                'moduleTasks',
            ])
            ->when($request->course_id, function ($query) use ($request) {
                $query->where('course_id', $request->course_id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search}%");
            })
            ->orderBy('step', 'asc')
            ->get();
    }

    /**
     * moduleNextStep
     *
     * @param  mixed $step
     * @return mixed
     */
    public function moduleNextStep(int $step, string $course_id): mixed
    {
        return $this->model->query()
            ->where('step', $step + 1)
            ->where('course_id', $course_id)->first();
    }
    /**
     * Method modulePrevStep
     *
     * @param int $step [explicite description]
     *
     * @return mixed
     */
    public function modulePrevStep(int $step, string $course_id): mixed
    {
        return $this->model->query()->where('step', $step - 1)->where('course_id', $course_id)->first();
    }

    /**
     * showWithSlug
     *
     * @param  mixed $slug
     * @return mixed
     */
    public function showWithSlug(string $slug): mixed
    {
        return $this->model->query()->where('slug', $slug)->firstOrFail();
    }

    /**
     * whereStepCourse
     *
     * @param  mixed $step
     * @return mixed
     */
    public function whereStepCourse(int $step, mixed $course_id): mixed
    {
        return $this->model->query()->where('step', $step)->where('course_id', $course_id)->firstOrFail();
    }

    /**
     * whereDevision
     *
     * @param  mixed $step
     * @return mixed
     */
    public function whereDivision(mixed $division_id): mixed
    {
        return $this->model->query()->whereRelation('course.courseLearningPaths.learningPath', 'division_id', $division_id)->get();
    }

    public function getTaskClearByModuleAndUser(mixed $module_id, mixed $user_id)
    {
        return $this->model->query()->find($module_id)
            ->moduleTasks()
            ->whereHas('submissionTask', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })->count();
    }

    public function bulkUpdateById(array $ids, array $data): mixed
    {
        return $this->model->query()
                ->whereIn('id', $ids)
                ->update($data);
    }
}
