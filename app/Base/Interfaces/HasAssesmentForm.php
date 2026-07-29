<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasAssesmentForm
{

    /**
     * One-to-Many relationship with school Model
     *
     * @return BelongsTo
     */

    public function assessmentForm(): BelongsTo;
}
