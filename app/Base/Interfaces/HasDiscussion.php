<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasDiscussion
{
    /**
     * Method course
     *
     * @return BelongsTo
     */
    public function discussion(): BelongsTo;
}
