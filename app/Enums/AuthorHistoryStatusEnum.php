<?php

namespace App\Enums;

enum AuthorHistoryStatusEnum: string
{
    case BANNED = 'banned';
    case REJECTED = 'rejected';
}
