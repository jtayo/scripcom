<?php

namespace App\Enums;

enum HotspotStatus: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Degraded = 'degraded';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Online => 'Online',
            self::Offline => 'Offline',
            self::Degraded => 'Degraded',
            self::Maintenance => 'Maintenance',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Online => 'success',
            self::Offline => 'danger',
            self::Degraded => 'warning',
            self::Maintenance => 'secondary',
        };
    }
}
