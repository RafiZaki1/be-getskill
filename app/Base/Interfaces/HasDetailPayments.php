<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasDetailPayments
{
    /**
     * Method course
     *
     * @return HasMany
     */
    public function detailPayments(): HasMany;
}
