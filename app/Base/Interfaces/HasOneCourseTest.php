<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface HasOneCourseTest
{
    /**
     * Method course
     *
     * @return HasOne
     */
    public function courseTest(): HasOne;
}
