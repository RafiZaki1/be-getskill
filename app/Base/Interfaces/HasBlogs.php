<?php

namespace App\Base\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasBlogs
{
    /**
     * Get all of the courses for the HasCourses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blogs(): HasMany;
}
