<?php

namespace App\Enums;

enum ChallengeSubmitEnum: string
{
    case CONFIRMED = 'confirmed';
    case NOT_CONFIRMED = 'not_confirmed';
}
