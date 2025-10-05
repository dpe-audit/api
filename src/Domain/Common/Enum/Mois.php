<?php

namespace App\Domain\Common\Enum;

enum Mois: string
{
    case JANVIER = '01';
    case FEVRIER = '02';
    case MARS = '03';
    case AVRIL = '04';
    case MAI = '05';
    case JUIN = '06';
    case JUILLET = '07';
    case AOUT = '08';
    case SEPTEMBRE = '09';
    case OCTOBRE = '10';
    case NOVEMBRE = '11';
    case DECEMBRE = '12';

    /**
     * Nombre de jours dans l'année
     */
    public final const NOMBRE_JOURS = 365;

    /**
     * Nombre de jours d'occupation sur l'année
     */
    public final const NOMBRE_JOURS_OCCUPATION = 358;

    public static function reduce(\Closure $func, mixed $initial = 0): mixed
    {
        return array_reduce(self::cases(), fn(float $carry, Mois $mois) => $carry += $func($mois), $initial);
    }

    public static function each(\Closure $func): array
    {
        return \array_map($func, self::cases());
    }

    /**
     * Nombre de jours d'occupation sur le mois
     */
    public function nj(): int
    {
        return match ($this) {
            self::JANVIER => 31,
            self::FEVRIER => 28,
            self::MARS => 31,
            self::AVRIL => 30,
            self::MAI => 31,
            self::JUIN => 30,
            self::JUILLET => 31,
            self::AOUT => 31,
            self::SEPTEMBRE => 30,
            self::OCTOBRE => 31,
            self::NOVEMBRE => 30,
            self::DECEMBRE => 24,
        };
    }

    /**
     * Nombre d'heures d'occupation sur le mois
     */
    public function nh(): int
    {
        return $this->nj() * 24;
    }
}
