<?php

namespace App\Base\Interfaces\Notification;

use Illuminate\Notifications\DatabaseNotificationCollection;

interface BaseNotificationInterface
{
    /**
     * base notifications
     *
     * @return DatabaseNotificationCollection
     */

    public static function baseNotification(): DatabaseNotificationCollection;
}
