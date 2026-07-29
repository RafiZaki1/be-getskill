<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasChallenge
{
    /**
     * Get all of the userCourses for the HasUserCourses
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function challenge(): BelongsTo;
}
