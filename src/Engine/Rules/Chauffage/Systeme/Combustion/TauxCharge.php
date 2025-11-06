<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

enum TauxCharge: int
{
    case TCH5 = 5;
    case TCH15 = 15;
    case TCH25 = 25;
    case TCH35 = 35;
    case TCH45 = 45;
    case TCH55 = 55;
    case TCH65 = 65;
    case TCH75 = 75;
    case TCH85 = 85;
    case TCH95 = 95;

    public function taux_charge(): float
    {
        return $this->value / 100;
    }

    public function coefficient_ponderation(): float
    {
        return match ($this) {
            self::TCH5 => 0.1,
            self::TCH15 => 0.25,
            self::TCH25 => 0.2,
            self::TCH35 => 0.15,
            self::TCH45 => 0.1,
            self::TCH55 => 0.1,
            self::TCH65 => 0.05,
            self::TCH75 => 0.025,
            self::TCH85 => 0.025,
            self::TCH95 => 0,
        };
    }
}
