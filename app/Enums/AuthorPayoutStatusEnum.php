<?php

namespace App\Enums;

enum AuthorPayoutStatusEnum: string
{
    case PENDING = 'pending';
    case CANCELED = 'canceled';
    case REJECTED = 'rejected';
    case PAID = 'paid';
}
