<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasAverageRating
{    
    /**
     * Method averageRating
     *
     * @return float
     */
    public function averageRating(): float;
}
