<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasBlog
{
    /**
     * Method blog
     *
     * @return BelongsTo
     */
    public function blog(): BelongsTo;
}
