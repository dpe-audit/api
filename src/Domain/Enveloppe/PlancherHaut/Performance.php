<?php

namespace App\Domain\Enveloppe\PlancherHaut;

/**
 * @see https://www.legifrance.gouv.fr/download/pdf?id=doxMrRr0wbfJVvtWjfDP4qE7zNsiFZL-4wqNyqoY-CA=
 */
enum Performance: string
{
    case TRES_BONNE = 'tres_bonne';
    case BONNE = 'bonne';
    case MOYENNE = 'moyenne';
    case INSUFFISANTE = 'insuffisante';

    public static function from_data(float $uph, Configuration $configuration): self
    {
        return match ($configuration) {
            Configuration::PLANCHER => match (true) {
                $uph > 0.3 => self::INSUFFISANTE,
                $uph > 0.2 => self::MOYENNE,
                $uph >= 0.15 => self::BONNE,
                $uph < 0.15 => self::TRES_BONNE,
            },
            Configuration::RAMPANTS => match (true) {
                $uph > 0.3 => self::INSUFFISANTE,
                $uph > 0.25 => self::MOYENNE,
                $uph >= 0.18 => self::BONNE,
                $uph < 0.18 => self::TRES_BONNE,
            },
            Configuration::TERRASSE => match (true) {
                $uph > 0.35 => self::INSUFFISANTE,
                $uph > 0.3 => self::MOYENNE,
                $uph >= 0.25 => self::BONNE,
                $uph < 0.25 => self::TRES_BONNE,
            }
        };
    }
}
