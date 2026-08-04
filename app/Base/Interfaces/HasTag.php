<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasTag
{
    /**
     * Method course
     *
     * @return BelongsTo
     */
    public function tag(): BelongsTo;
}
