<?php

namespace App\Contracts\Interfaces\Auth;

use App\Contracts\Interfaces\Eloquent\BaseInterface;

interface UserInterface extends BaseInterface
{
    /**
     * Get user by email.
     *
     * @param string $email
     * @return mixed
     */
    public function getByEmail(string $email);
}
