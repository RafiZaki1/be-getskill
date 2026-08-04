<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasDiscussionAnswers
{
    /**
     * Method course
     *
     * @return HasMany
     */
    public function discussionAnswers(): HasMany;
}
