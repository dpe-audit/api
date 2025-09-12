<?php

namespace App\Domain\Enveloppe\PlancherBas;

/**
 * @see https://www.legifrance.gouv.fr/download/pdf?id=doxMrRr0wbfJVvtWjfDP4qE7zNsiFZL-4wqNyqoY-CA=
 */
enum Performance: string
{
    case TRES_BONNE = 'tres_bonne';
    case BONNE = 'bonne';
    case MOYENNE = 'moyenne';
    case INSUFFISANTE = 'insuffisante';

    public static function from_data(float $upb): self
    {
        return match (true) {
            $upb > 0.65 => self::INSUFFISANTE,
            $upb > 0.45 => self::MOYENNE,
            $upb >= 0.25 => self::BONNE,
            $upb < 0.25 => self::TRES_BONNE,
        };
    }
}
