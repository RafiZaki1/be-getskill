<?php

namespace App\Contracts\Interfaces\Course;

use Illuminate\Http\JsonResponse;
use App\Contracts\Interfaces\Eloquent\GetInterface;
use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\SearchInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;
use App\Contracts\Interfaces\Eloquent\GetWhereInterface;
use App\Contracts\Interfaces\Eloquent\ShowSlugInterface;
use App\Contracts\Interfaces\Eloquent\CustomPaginationInterface;
use Illuminate\Http\Request;

interface CourseInterface extends CustomPaginationInterface, StoreInterface, UpdateInterface, ShowInterface, DeleteInterface, SearchInterface, GetInterface
{
    public function count(): mixed;
    public function topRatings(): mixed;

    /**
     * showWithSlug
     *
     * @param  mixed $request
     * @param  mixed $slug
     * @return mixed
     */
    public function showWithSlug(Request $request, string $slug): mixed;

    /**
     * showWithSlug
     *
     * @param  mixed $request
     * @param  mixed $slug
     * @return mixed
     */
    public function showWithSlugWithoutRequest(string $slug): mixed;
    /**
     * Method getTop
     *
     * @return mixed
     */
    public function getTop(): mixed;
    /**
     * Method getSome
     *
     * @return mixed
     */
    public function getSome(Request $request): mixed;
    public function getSome2(Request $request): mixed;
    public function getCourseById(mixed $courseId): mixed;
    public function getByCourseIds(array $courseIds);
    public function getByCourseIdsWithRelations(array $courseIds);
    public function getBySubModuleSlug(string $slug);
}
