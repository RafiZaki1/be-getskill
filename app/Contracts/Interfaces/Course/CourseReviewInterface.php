<?php

namespace App\Contracts\Interfaces\Course;

use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Interfaces\Eloquent\CustomPaginationInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\GetInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;

interface CourseReviewInterface extends GetInterface, StoreInterface, ShowInterface, UpdateInterface, DeleteInterface
{
    public function latest(int $limit): mixed;    
    /**
     * Method getLatest
     *
     * @return mixed
     */
    public function getLatest(): mixed;
}