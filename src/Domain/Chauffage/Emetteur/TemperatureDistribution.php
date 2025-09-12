<?php

namespace App\Domain\Chauffage\Emetteur;

enum TemperatureDistribution: string
{
    case BASSE = 'basse';
    case MOYENNE = 'moyenne';
    case HAUTE = 'haute';

    /**
     * Chute nominale de température de dimensionnement en °C
     */
    public function chute_nominale_temperature(): float
    {
        return match ($this) {
            self::BASSE => 7.5,
            self::MOYENNE => 7.5,
            self::HAUTE => 15,
        };
    }
}
