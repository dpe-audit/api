<?php

namespace App\Domain\Batiment;

enum ClasseAltitude: string
{
    case _400_LT = '400_lt';
    case _400_800 = '400_800';
    case _800_GT = '800_gt';

    public static function from_opendata(string $value): self
    {
        return match ($value) {
            'inférieur à 400m' => self::_400_LT,
            '400-800m' => self::_400_800,
            'supérieur à 800m' => self::_800_GT,
            default => self::_400_LT
        };
    }

    public static function fromAltitude(int|float $value): self
    {
        return match (true) {
            $value < 400 => self::_400_LT,
            $value <= 800 => self::_400_800,
            $value > 800 => self::_800_GT,
        };
    }

    public function altitude(): float
    {
        return match ($this) {
            self::_400_LT => 200,
            self::_400_800 => 600,
            self::_800_GT => 1000,
        };
    }

    public function min(): ?float
    {
        return match ($this) {
            self::_400_LT => 0,
            self::_400_800 => 400,
            self::_800_GT => 801,
        };
    }

    public function max(): ?float
    {
        return match ($this) {
            self::_400_LT => 399,
            self::_400_800 => 800,
            self::_800_GT => null,
        };
    }
}
