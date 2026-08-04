<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface HasOneStudent
{
    /**
     * Method student
     *
     * @return HasOne
     */
    public function student(): HasOne;
}
