<?php

namespace App\Enums;

enum UserEventStatusEnum: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case CANCELED = 'canceled';
    case DECLINED = 'declined';
}
