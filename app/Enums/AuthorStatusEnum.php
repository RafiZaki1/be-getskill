<?php

namespace App\Enums;

enum AuthorStatusEnum: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case BANNED = 'banned';
    case REJECTED = 'rejected';
}
