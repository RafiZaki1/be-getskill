<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasCourse
{
    /**
     * Method course
     *
     * @return BelongsTo
     */
    public function course(): BelongsTo;
}
