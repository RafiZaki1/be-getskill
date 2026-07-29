<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasEvent
{
    /**
     * Method course
     *
     * @return BelongsTo
     */
    public function event(): BelongsTo;
}
