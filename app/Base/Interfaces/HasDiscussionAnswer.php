<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasDiscussionAnswer
{
    /**
     * Method course
     *
     * @return HasMany
     */
    public function discussionAnswers(): HasMany;
}
