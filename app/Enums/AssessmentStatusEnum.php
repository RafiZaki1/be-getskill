<?php

namespace App\Enums;

enum AssessmentStatusEnum: string
{
    case NOT_STARTED = 'not_started';
    case PARTIAL = 'partial';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
}
