<?php

namespace App\Domain\Enveloppe\Paroi;

enum TypeParoi: string
{
    case MUR = 'mur';
    case PLANCHER_BAS = 'plancher_bas';
    case PLANCHER_HAUT = 'plancher_haut';
    case BAIE = 'baie';
    case PORTE = 'porte';

    public static function each(\Closure $func): array
    {
        return \array_map($func, self::cases());
    }

    /**
     * @return self[]
     */
    public static function parois_opaques(): array
    {
        return [self::MUR, self::PLANCHER_BAS, self::PLANCHER_HAUT];
    }

    /**
     * @return self[]
     */
    public static function ouvertures(): array
    {
        return [self::BAIE, self::PORTE];
    }
}
