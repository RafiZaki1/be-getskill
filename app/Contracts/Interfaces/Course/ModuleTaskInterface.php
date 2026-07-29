<?php

namespace App\Contracts\Interfaces\Course;

use Illuminate\Http\Request;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;
use App\Contracts\Interfaces\Eloquent\GetWhereInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface ModuleTaskInterface extends GetWhereInterface, StoreInterface, UpdateInterface, ShowInterface, DeleteInterface
{
    public function getByCourse(string $slug);
    public function getByCourseWithPaginate(Request $request, string $slug, int $pagination = 10, ): LengthAwarePaginator;
    public function searchUser(Request $request);
}