<?php

namespace App\Base\Container;

use App\Base\Interfaces\Notification\BaseNotificationInterface;
use Illuminate\Notifications\DatabaseNotificationCollection;

abstract class NotificationContainer implements BaseNotificationInterface
{
    /**
     * base notifications
     *
     * @return DatabaseNotificationCollection
     */

    public static function baseNotification(): DatabaseNotificationCollection
    {
        return auth()->user()->unreadNotifications;
    }
}
