<?php

namespace App\Observers;

use App\Models\Course;
use Faker\Provider\Uuid;
use Illuminate\Support\Str;

class CourseObserver
{
    public function creating(Course $course): void
    {
        $course->slug = Str::slug($course->title);
    }
    /**
     * updating
     *
     * @param  mixed $course
     * @return void
     */
    public function updating(Course $course): void
    {
        $course->slug = Str::slug($course->title);
    }
}
