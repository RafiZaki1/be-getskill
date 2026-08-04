<?php

namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\SubModuleInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\Module;
use App\Models\SubModule;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubModuleRepository extends BaseRepository implements SubModuleInterface
{

    /**
     * Method __construct
     *
     * @param SubModule $subModule [explicite description]
     *
     * @return void
     */
    public function __construct(SubModule $subModule)
    {
        $this->model = $subModule;
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
            ->when($request->search, function ($query) use ($request) {
                $query->whereLike('title', $request->search);
            })->when($request->module_id, function ($query) use ($request) {
                $query->where('module_id', $request->module_id);
            })
            ->paginate($pagination);
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
     * getOneByModul
     *
     * @param  mixed $id
     * @return mixed
     */
    public function getOneByModul(string $id): mixed
    {
        return $this->model->query()
            ->where('module_id', $id)
            ->latest()
            ->first();
    }

    /**
     * showWithSlug
     *
     * @param  mixed $slug
     * @return mixed
     */
    public function showWithSlug(string $slug): mixed
    {
        return $this->model->query()
            ->where('slug', $slug)->first();
    }

    /**
     * nextSubModule
     *
     * @return mixed
     */
    public function nextSubModule(mixed $step, mixed $module_id): mixed
    {
        return $this->model->query()->where('module_id', $module_id)->where('step', $step)->first();
    }
    /**
     * Method prevSubModule
     *
     * @param mixed $step [explicite description]
     * @param mixed $module_id [explicite description]
     *
     * @return mixed
     */
    public function prevSubModule(mixed $step, mixed $module_id): mixed
    {
        return $this->model->query()->where('module_id', $module_id)->where('step', $step)->first();
    }


    /**
     * getAllPrevSubModule
     *
     * @param  mixed $sub_module_id
     * @return mixed
     */
    public function getAllPrevSubModule(mixed $sub_module_step, mixed $module_id): mixed
    {
        return $this->model->query()->where('step', '<=', $sub_module_step)->where('module_id', $module_id)->get();
    }

    public function bulkUpdateById(array $ids, array $data): mixed
    {
        return $this->model->query()
            ->whereIn('id', $ids)
            ->update($data);
    }

    /**
     * Menukar step antara dua sub_module secara atomic (dibungkus transaction)
     * supaya tidak ada state pertengahan yang bisa gagal/duplicate.
     *
     * @param SubModule $current
     * @param SubModule $target
     * @return void
     */
    public function swapStep(SubModule $current, SubModule $target): void
    {
        DB::transaction(function () use ($current, $target) {
            $currentStep = $current->step;
            $targetStep = $target->step;

            // Update langsung ke nilai final, bukan increment/decrement
            // supaya tidak bergantung pada nilai DB "live" yang bisa berubah di tengah proses
            $current->update(['step' => $targetStep]);
            $target->update(['step' => $currentStep]);
        });
    }

    /**
     * Method getForward
     *
     * @param mixed $step [explicite description]
     * @param string $moduleId [explicite description]
     *
     * @return mixed
     */
    public function getForward(mixed $step, string $moduleId): mixed
    {
        return $this->model
            ->query()
            ->where('module_id', $moduleId)
            ->where('step', '>', $step)
            ->orderBy('step', 'ASC')
            ->first();
    }

    /**
     * Method getBackward
     *
     * @param mixed $step [explicite description]
     * @param string $moduleId [explicite description]
     *
     * @return mixed
     */
    public function getBackward(mixed $step, string $moduleId): mixed
    {
        return $this->model
            ->query()
            ->where('module_id', $moduleId)
            ->where('step', '<', $step)
            ->orderByDesc('step')
            ->first();
    }
}
