<?php

namespace App\Base\Interfaces;

use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasSocialAccount
{

    /**
     * Get all of the comments for the HasSocialAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialAccounts(): HasMany;

}
