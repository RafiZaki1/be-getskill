<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasSchool
{

    /**
     * One-to-Many relationship with school Model
     *
     * @return BelongsTo
     */

    public function school(): BelongsTo;
}
