<?php

namespace App\Base\Interfaces\Notification;

interface TakeInterface
{
    /**
     * Get all notifications with provided parameter
     *
     * @param int $take
     *
     * @return object|null
     */

    public static function take(int $take = 10): object|null;
}
