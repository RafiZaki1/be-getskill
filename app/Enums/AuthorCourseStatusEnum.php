<?php

namespace App\Enums;

enum AuthorCourseStatusEnum: string
{
    case IN_PROGRESS = 'in_progress';
    case RETURNED = 'returned';
    case REJECTED = 'rejected';
    case ACCEPTED = 'accepted';
}
