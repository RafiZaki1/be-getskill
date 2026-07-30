<?php

namespace App\Services\Course;

use App\Contracts\Interfaces\Course\PostTestIdInterface;
use App\Models\CourseTest;

class CourseTestService implements PostTestIdInterface
{
    /**
     * Get the post test ID from a CourseTest.
     *
     * @param CourseTest $courseTest
     * @return int
     */
    public function getPostTestId(CourseTest $courseTest): int
    {
        return $courseTest->id;
    }
}
