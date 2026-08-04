<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasStudent
{
    /**
     * Method student
     *
     * @return BelongsTo
     */
    public function student(): BelongsTo;
}
