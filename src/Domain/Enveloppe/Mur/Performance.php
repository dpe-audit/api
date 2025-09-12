<?php

namespace App\Domain\Enveloppe\Mur;

/**
 * @see https://www.legifrance.gouv.fr/download/pdf?id=doxMrRr0wbfJVvtWjfDP4qE7zNsiFZL-4wqNyqoY-CA=
 */
enum Performance: string
{
    case TRES_BONNE = 'tres_bonne';
    case BONNE = 'bonne';
    case MOYENNE = 'moyenne';
    case INSUFFISANTE = 'insuffisante';

    public static function from_data(float $umur): self
    {
        return match (true) {
            $umur > 0.65 => self::INSUFFISANTE,
            $umur > 0.45 => self::MOYENNE,
            $umur >= 0.3 => self::BONNE,
            $umur < 0.3 => self::TRES_BONNE,
        };
    }
}
