<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasTags
{
    /**
     * Method course
     *
     * @return HasMany
     */
    public function tags(): HasMany;
}
