<?php

namespace App\Contracts\Interfaces\Course;

use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Interfaces\Eloquent\CustomPaginationInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\SearchInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;
use Illuminate\Http\Request;

interface UserCourseInterface extends CustomPaginationInterface, StoreInterface, DeleteInterface, ShowInterface, UpdateInterface
{
    /**
     * Method showByCourse
     *
     * @param $courseId $courseId [explicit description]
     *
     * @return mixed
     */
    public function showByCourse($courseId): mixed;

    /**
     * Method customUpdate
     *
     * @param mixed $courseId [explicit description]
     * @param array $data [explicit description]
     *
     * @return mixed
     */
    public function customUpdate(mixed $courseId, array $data): mixed;

    /**
     * Method checkByCourse
     *
     * @param mixed $courseId [explicit description]
     *
     * @return mixed
     */
    public function checkByCourse($courseId, $user_id = null): mixed;

    /**
     * Method to remove the 'has_pre_test' field for a user in the given course
     *
     * @param mixed $courseId [explicit description]
     *
     * @return mixed
     */
    public function removeHasPreTest(mixed $courseId): mixed;

    /**
     * Method to remove the 'has_post_test' field for a user in the given course
     *
     * @param mixed $courseId [explicit description]
     *
     * @return mixed
     */
    public function courseActivity(Request $request, int $pagination = 10): mixed;

    /**
     * Method to get by course and userId
     *
     * @param string $courseId
     * @param string $userId
     * @return mixed
     */
    public function findByCourseAndUser($courseId, $userId): mixed;
}
