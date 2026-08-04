<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasCourseTest
{

    /**
     * One-to-Many relationship with User Model
     *
     * @return BelongsTo
     */

    public function courseTest(): BelongsTo;
}
