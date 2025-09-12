<?php

namespace App\Domain\Enveloppe\Porte;

/**
 * @see https://www.legifrance.gouv.fr/download/pdf?id=doxMrRr0wbfJVvtWjfDP4qE7zNsiFZL-4wqNyqoY-CA=
 */
enum Performance: string
{
    case TRES_BONNE = 'tres_bonne';
    case BONNE = 'bonne';
    case MOYENNE = 'moyenne';
    case INSUFFISANTE = 'insuffisante';

    public static function from_data(float $uporte): self
    {
        return match (true) {
            $uporte > 3 => self::INSUFFISANTE,
            $uporte > 2.2 => self::MOYENNE,
            $uporte >= 1.6 => self::BONNE,
            $uporte < 1.6 => self::TRES_BONNE,
        };
    }
}
