<?php

namespace App\Domain\Common\Enum;

enum Orientation: string
{
    case NORD = 'nord';
    case EST = 'est';
    case SUD = 'sud';
    case OUEST = 'ouest';

    public static function from_azimut(float $azimut): self
    {
        return match (true) {
            $azimut <= 45, $azimut >= 315 => self::NORD,
            $azimut > 45 && $azimut < 135 => self::EST,
            $azimut >= 135 && $azimut <= 225 => self::SUD,
            $azimut > 225 && $azimut < 315 => self::OUEST,
        };
    }

    public function azimut(): float
    {
        return match ($this) {
            self::NORD => 0,
            self::EST => 90,
            self::SUD => 180,
            self::OUEST => 270,
        };
    }
}
