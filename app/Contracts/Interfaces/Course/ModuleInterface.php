<?php

namespace App\Contracts\Interfaces\Course;

use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Interfaces\Eloquent\CustomPaginationInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\GetWhereInterface;
use App\Contracts\Interfaces\Eloquent\SearchInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\ShowSlugInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;

interface ModuleInterface extends CustomPaginationInterface, StoreInterface, ShowInterface, UpdateInterface, DeleteInterface, SearchInterface, ShowSlugInterface
{
    /**
     * getOneByCourse
     *
     * @param  mixed $id
     * @return mixed
     */
    public function getOneByCourse(string $id): mixed;

    public function shiftStepsUpward(int $fromStep, string $courseId, string $excludeId): void;

    public function normalizeSteps(string $courseId): void;
    /**
     * Method getOneStep
     *
     * @param mixed $mixed [explicite description]
     *
     * @return mixed
     */
    public function getForward(mixed $step, string $id): mixed;
    /**
     * Method getOneStepBackward
     *
     * @param mixed $mixed [explicite description]
     *
     * @return mixed
     */
    public function getBackward(mixed $step, string $id): mixed;
    /**
     * Method getWhere
     *
     * @param string $column [explicite description]
     * @param string $operator [explicite description]
     * @param $value $value [explicite description]
     *
     * @return mixed
     */
    public function getWhere(string $column, string $operator, $value): mixed;

    /**
     * moduleNextStep
     *
     * @return mixed
     */
    public function moduleNextStep(int $step, string $course_id): mixed;
    /**
     * Method modulePrevStep
     *
     * @param int $step [explicite description]
     *
     * @return mixed
     */
    public function modulePrevStep(int $step, string $course_id): mixed;


    /**
     * whereStepCourse
     *
     * @param  mixed $step
     * @return mixed
     */
    public function whereStepCourse(int $step, mixed $course_id): mixed;



    /**
     * Perform bulk update on ids
     *
     * @param array $ids
     * @param array $data
     * @return mixed
     */
    public function bulkUpdateById(array $ids, array $data): mixed;
}
