<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasStudentClassrooms
{
    /**
     * Method student
     *
     * @return HasMany
     */
    public function studentClassrooms(): HasMany;
}
