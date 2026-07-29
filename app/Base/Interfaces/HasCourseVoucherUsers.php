<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasCourseVoucherUsers
{
    /**
     * Get all of the comments for the HasCourseVoucherUsers
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseVoucherUsers(): HasMany;
}
