<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasDiscussionTags
{
    /**
     * Method course
     *
     * @return HasMany
     */
    public function discussionTags(): HasMany;
}
