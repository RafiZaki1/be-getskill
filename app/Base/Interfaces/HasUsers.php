<?php

namespace App\Base\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasUsers
{
    /**
     * Get all of the users for the HasUsers
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany;
}
