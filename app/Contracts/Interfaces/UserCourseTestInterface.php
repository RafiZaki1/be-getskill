<?php

namespace App\Contracts\Interfaces;

use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Interfaces\Eloquent\CustomPaginationInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\GetInterface;
use App\Contracts\Interfaces\Eloquent\SearchInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\ShowSlugInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserCourseTestInterface extends GetInterface, StoreInterface, CustomPaginationInterface,UpdateInterface,ShowInterface
{
    public function getByClassroom(mixed $data): mixed;
    
    /**
     * Method to remove the score for a given UserCourseTest
     *
     * @param mixed $userCourseTestId
     * @return mixed
     */
    public function removeScore(mixed $userCourseTestId): mixed;
    public function getStatsByClassroom(string $classroom): mixed;

    /**
     * get user course by classroom and course slug with filter and paginate
     *
     * @param Request $request
     * @param string $classroom_id
     * @param string $slug
     * @param integer $paginate
     * @return LengthAwarePaginator
     */
    public function getByClassroomAndCourse(Request $request, string $classroom_id, string $slug, int $paginate = 10): LengthAwarePaginator;
}
