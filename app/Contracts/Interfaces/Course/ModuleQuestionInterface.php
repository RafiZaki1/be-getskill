<?php

namespace App\Contracts\Interfaces\Course;

use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Interfaces\Eloquent\CustomPaginationInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\GetInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ModuleQuestionInterface extends CustomPaginationInterface, GetInterface, StoreInterface, UpdateInterface, ShowInterface, DeleteInterface
{
    /**
     * Handle the Get all data event from models.
     *
     * @return mixed
     */

    public function getByModule(string $id): mixed;
    /**
     * Method getQuestions
     *
     * @param mixed $id [explicite description]
     * @param mixed $total [explicite description]
     *
     * @return mixed
     */
    public function getQuestions(mixed $id, mixed $total): mixed;
}
