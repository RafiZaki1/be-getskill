<?php

namespace App\Contracts\Interfaces\Course;

use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Interfaces\Eloquent\CustomPaginationInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\ShowSlugInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;
use App\Models\SubModule;

interface SubModuleInterface extends CustomPaginationInterface, StoreInterface, UpdateInterface, ShowInterface, DeleteInterface, ShowSlugInterface
{
    /**
     * getOneByModul
     *
     * @param  mixed $id
     * @return mixed
     */
    public function getOneByModul(string $id): mixed;

    /**
     * nextSubModule
     *
     * @return void
     */
    public function nextSubModule(mixed $step, mixed $module_id): mixed;
    /**
     * Method prevSubModule
     *
     * @param mixed $step [explicite description]
     * @param mixed $module_id [explicite description]
     *
     * @return mixed
     */
    public function prevSubModule(mixed $step, mixed $module_id): mixed;


    /**
     * getAllPrevSubModule
     *
     * @param  mixed $step
     * @param  mixed $sub_module_id
     * @return mixed
     */
    public function getAllPrevSubModule(mixed $sub_module_step, mixed $module_id): mixed;

    /**
     * Perform bulk update on ids
     *
     * @param array $ids
     * @param array $data
     * @return mixed
     */
    public function bulkUpdateById(array $ids, array $data): mixed;

    /**
     * Menukar step antara dua sub_module secara atomic (dibungkus transaction)
     * supaya tidak ada state pertengahan yang bisa gagal/duplicate.
     *
     * @param SubModule $current
     * @param SubModule $target
     * @return void
     */
    public function swapStep(SubModule $current, SubModule $target): void;

    /**
     * Method getForward
     *
     * @param mixed $step [explicite description]
     * @param string $moduleId [explicite description]
     *
     * @return mixed
     */
    public function getForward(mixed $step, string $moduleId): mixed;

    /**
     * Method getBackward
     *
     * @param mixed $step [explicite description]
     * @param string $moduleId [explicite description]
     *
     * @return mixed
     */
    public function getBackward(mixed $step, string $moduleId): mixed;
}
