<?php

namespace App\Enums;

enum StatusEnum: string
{
    case APPROVED = 'approved';
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case RETURNED = 'returned';
}
