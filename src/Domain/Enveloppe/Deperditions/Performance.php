<?php

namespace App\Domain\Enveloppe\Deperditions;

/**
 * @see https://www.legifrance.gouv.fr/download/pdf?id=doxMrRr0wbfJVvtWjfDP4qE7zNsiFZL-4wqNyqoY-CA=
 */
enum Performance: string
{
    case TRES_BONNE = 'tres_bonne';
    case BONNE = 'bonne';
    case MOYENNE = 'moyenne';
    case INSUFFISANTE = 'insuffisante';

    public static function from_data(float $ubat): self
    {
        return match (true) {
            $ubat > 0.85 => self::INSUFFISANTE,
            $ubat > 0.65 => self::MOYENNE,
            $ubat > 0.45 => self::BONNE,
            $ubat <= 0.45 => self::TRES_BONNE,
        };
    }
}
