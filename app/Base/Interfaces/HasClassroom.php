<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasClassroom
{
    /**
     * Method student
     *
     * @return BelongsTo
     */
    public function classroom(): BelongsTo;
}
