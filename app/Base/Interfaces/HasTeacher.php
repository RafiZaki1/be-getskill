<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasTeacher
{

    /**
     * One-to-Many relationship with User Model
     *
     * @return BelongsTo
     */

    public function teacher(): BelongsTo;
}
