<?php

namespace App\Base\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasZooms
{
    /**
     * Get all of the users for the HasUsers
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zooms(): HasMany;
}
