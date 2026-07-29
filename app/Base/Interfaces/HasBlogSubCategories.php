<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasBlogSubCategories
{
    /**
     * Get all of the categories for the HasCategories
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function BlogSubCategories(): HasMany;
}
