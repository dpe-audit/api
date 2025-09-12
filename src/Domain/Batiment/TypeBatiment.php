<?php

namespace App\Domain\Batiment;

enum TypeBatiment: string
{
    case MAISON = 'maison';
    case IMMEUBLE = 'immeuble';

    public static function from_opendata(string $type): self
    {
        return match ($type) {
            'maison' => self::MAISON,
            'appartement' => self::IMMEUBLE,
            'immeuble' => self::IMMEUBLE,
        };
    }

    public static function from_nombre_logements(int $logements): self
    {
        return $logements > 2 ? self::IMMEUBLE : self::MAISON;
    }

    /**
     * Ratio du temps d'utilisation pour les ventilations hybrides (1 par défaut)
     */
    public function ratio_temps_utilisation_ventilation(): ?float
    {
        return match ($this->valeur) {
            self::MAISON => 0.083,
            //self::APPARTEMENT => 0.167,
            self::IMMEUBLE => 0.167,
            default => 1
        };
    }
}
