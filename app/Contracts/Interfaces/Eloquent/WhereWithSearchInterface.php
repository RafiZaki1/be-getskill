<?php

namespace App\Contracts\Interfaces\Eloquent;

use Illuminate\Http\Request;

interface WhereWithSearchInterface
{
    /**
     * Handle get data event from models.
     *
     * @param array $data
     * 
     * @return mixed
     */

    public function whereWithSearch(array $data, Request $request): mixed;
}
