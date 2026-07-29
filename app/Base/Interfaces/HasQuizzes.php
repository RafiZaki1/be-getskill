<?php

namespace App\Base\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasQuizzes
{
    /**
     * Get all of the quizzes for the HasCourses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quizzes(): HasMany;
}
