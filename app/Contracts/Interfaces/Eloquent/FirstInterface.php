<?php

namespace App\Contracts\Interfaces\Eloquent;

interface FirstInterface
{
    /**
     * Handle show method and delete data instantly from models.
     *
     * @param mixed $id
     *
     * @return mixed
     */

    public function first(mixed $query): mixed;
}
