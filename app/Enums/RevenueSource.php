<?php

namespace App\Enums;

enum RevenueSource: string
{
    case DirectUser = 'direct_user';
    case County = 'county';
    case Corporate = 'corporate';
    case Ngo = 'ngo';
    case Institutional = 'institutional';
    case Advertising = 'advertising';
    case Voucher = 'voucher';

    public function label(): string
    {
        return match ($this) {
            self::DirectUser => 'Direct User',
            self::County => 'County',
            self::Corporate => 'Corporate',
            self::Ngo => 'NGO',
            self::Institutional => 'Institutional',
            self::Advertising => 'Advertising',
            self::Voucher => 'Voucher',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DirectUser => 'primary',
            self::County => 'success',
            self::Corporate => 'info',
            self::Ngo => 'purple',
            self::Institutional => 'indigo',
            self::Advertising => 'warning',
            self::Voucher => 'cyan',
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
