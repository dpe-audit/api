<?php

namespace App\Domain\Enveloppe\Baie;

/**
 * @see https://www.legifrance.gouv.fr/download/pdf?id=doxMrRr0wbfJVvtWjfDP4qE7zNsiFZL-4wqNyqoY-CA=
 */
enum Performance: string
{
    case TRES_BONNE = 'tres_bonne';
    case BONNE = 'bonne';
    case MOYENNE = 'moyenne';
    case INSUFFISANTE = 'insuffisante';

    public static function from_data(float $ubaie): self
    {
        return match (true) {
            $ubaie > 3 => self::INSUFFISANTE,
            $ubaie > 2.2 => self::MOYENNE,
            $ubaie >= 1.6 => self::BONNE,
            $ubaie < 1.6 => self::TRES_BONNE,
        };
    }
}
