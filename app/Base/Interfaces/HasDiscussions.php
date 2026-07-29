<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasDiscussions
{
    /**
     * Method course
     *
     * @return HasMany
     */
    public function discussions(): HasMany;
}
