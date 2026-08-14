<?php

namespace App\Enums;

enum SessionStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Expired = 'expired';
    case Revoked = 'revoked';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Completed => 'Completed',
            self::Expired => 'Expired',
            self::Revoked => 'Revoked',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Completed => 'secondary',
            self::Expired => 'warning',
            self::Revoked => 'danger',
            self::Failed => 'danger',
        };
    }
}
