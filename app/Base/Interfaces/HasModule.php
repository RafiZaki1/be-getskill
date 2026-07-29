<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasModule
{

    /**
     * One-to-Many relationship with User Model
     *
     * @return BelongsTo
     */

    public function module(): BelongsTo;
}
