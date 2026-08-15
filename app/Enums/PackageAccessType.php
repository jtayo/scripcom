<?php

namespace App\Enums;

enum PackageAccessType: string
{
    case Free = 'free';

    case Paid = 'paid';

    case Sponsored = 'sponsored';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free',
            self::Paid => 'Paid',
            self::Sponsored => 'Sponsored',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Free => 'success',
            self::Paid => 'warning',
            self::Sponsored => 'primary',
        };
    }
}
