<?php

namespace App\Enums;

enum PresenceEnum: string
{
    case PRESENT = 'present';
    case ALPHA = 'alpha';
    case PERMIT = 'permit';
    case SICK = 'sick';

    /**
     * Letter used for recap tables.
     */
    public function letter(): string
    {
        return match($this) {
            self::PRESENT => 'H', // Hadir
            self::ALPHA => 'A',   // Alpha
            self::PERMIT => 'I',  // Izin
            self::SICK => 'S',    // Sakit
        };
    }

    /**
     * Attempt to build enum from raw status; returns null if invalid or null.
     */
    public static function fromStatus(?string $status): ?self
    {
        return $status ? self::tryFrom($status) : null;
    }
}
