<?php

namespace App\Base\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasBlogViews
{
    /**
     * Get all of the blogViews for the HasCourses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blogViews(): HasMany;
}
