<?php

namespace App\Domain\Enveloppe\Paroi;

use App\Domain\Enveloppe\PlancherHaut\Configuration;

/**
 * @see https://www.legifrance.gouv.fr/download/pdf?id=doxMrRr0wbfJVvtWjfDP4qE7zNsiFZL-4wqNyqoY-CA=
 */
enum Performance: string
{
    case TRES_BONNE = 'tres_bonne';
    case BONNE = 'bonne';
    case MOYENNE = 'moyenne';
    case INSUFFISANTE = 'insuffisante';

    public static function from_umur(float $umur): self
    {
        return match (true) {
            $umur > 0.65 => self::INSUFFISANTE,
            $umur > 0.45 => self::MOYENNE,
            $umur >= 0.3 => self::BONNE,
            $umur < 0.3 => self::TRES_BONNE,
        };
    }

    public static function from_upb(float $upb): self
    {
        return match (true) {
            $upb > 0.65 => self::INSUFFISANTE,
            $upb > 0.45 => self::MOYENNE,
            $upb >= 0.25 => self::BONNE,
            $upb < 0.25 => self::TRES_BONNE,
        };
    }

    public static function from_uph(float $uph, Configuration $configuration): self
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

    public static function from_ubaie(float $ubaie): self
    {
        return match (true) {
            $ubaie > 3 => self::INSUFFISANTE,
            $ubaie > 2.2 => self::MOYENNE,
            $ubaie >= 1.6 => self::BONNE,
            $ubaie < 1.6 => self::TRES_BONNE,
        };
    }

    public static function from_uporte(float $uporte): self
    {
        return match (true) {
            $uporte > 3 => self::INSUFFISANTE,
            $uporte > 2.2 => self::MOYENNE,
            $uporte >= 1.6 => self::BONNE,
            $uporte < 1.6 => self::TRES_BONNE,
        };
    }
}
