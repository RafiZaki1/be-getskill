<?php

namespace App\Contracts\Interfaces\Course;

use App\Models\CourseTest;
use Illuminate\Http\Request;
use App\Contracts\Interfaces\Eloquent\GetInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;
use App\Contracts\Interfaces\Eloquent\ShowSlugInterface;

interface PostTestIdInterface
{
    /** 
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException 
     */
    public function getPostTestId(CourseTest $courseTest): int;
}
