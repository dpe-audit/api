<?php

namespace App\Domain\Logement;

enum Typologie: string
{
    case T1 = 'T1';
    case T2 = 'T2';
    case T3 = 'T3';
    case T4 = 'T4';
    case T5 = 'T5';
    case T6 = 'T6';
    case T7 = 'T7';

    public static function from_surface_habitable(float $surface_habitable): self
    {
        return match (true) {
            $surface_habitable <= 30 => self::T1,
            $surface_habitable <= 50 => self::T2,
            $surface_habitable <= 70 => self::T3,
            $surface_habitable <= 90 => self::T4,
            $surface_habitable <= 110 => self::T5,
            $surface_habitable <= 130 => self::T6,
            default => self::T7,
        };
    }
}
