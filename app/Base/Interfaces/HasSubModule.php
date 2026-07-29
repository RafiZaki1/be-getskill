<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasSubModule
{
    /**
     * Method course
     *
     * @return BelongsTo
     */
    public function subModule(): BelongsTo;
}
