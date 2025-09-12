<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Batiment\PeriodeConstruction;
use App\Domain\Batiment\TypeBatiment;

final class XMLCaracteristiqueGenerale
{
    public function __construct(
        public readonly ?int $annee_construction,
        public readonly int $enum_periode_construction_id,
        public readonly int $enum_methode_application_dpe_log_id,
        public readonly ?int $enum_calcul_echantillonnage_id,
        public readonly ?float $surface_habitable_logement,
        public readonly ?int $nombre_niveau_immeuble,
        public readonly ?int $nombre_niveau_logement,
        public readonly float $hsp,
        public readonly ?float $surface_habitable_immeuble,
        public readonly ?float $surface_tertiaire_immeuble,
        public readonly ?int $nombre_appartement,
        public readonly ?bool $appartement_non_visite,
        public readonly ?int $enum_scenario_id
    ) {}

    /**
     * XSD /logement/caracteristique_generale
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            annee_construction: (int) $xml->annee_construction ?: null,
            enum_periode_construction_id: (int) $xml->enum_periode_construction_id,
            enum_methode_application_dpe_log_id: (int) $xml->enum_methode_application_dpe_log_id,
            enum_calcul_echantillonnage_id: (int) $xml->enum_calcul_echantillonnage_id ?: null,
            surface_habitable_logement: (float) $xml->surface_habitable_logement ?: null,
            nombre_niveau_immeuble: (int) $xml->nombre_niveau_immeuble ?: null,
            nombre_niveau_logement: (int) $xml->nombre_niveau_logement ?: null,
            hsp: (float) $xml->hsp,
            surface_habitable_immeuble: (float) $xml->surface_habitable_immeuble ?: null,
            surface_tertiaire_immeuble: (float) $xml->surface_tertiaire_immeuble ?: null,
            nombre_appartement: (int) $xml->nombre_appartement ?: null,
            appartement_non_visite: (bool)(int) $xml->appartement_non_visite ?: null,
            enum_scenario_id: (int) $xml->enum_scenario_id ?: null
        );
    }

    public function type_batiment(): TypeBatiment
    {
        return match ($this->enum_methode_application_dpe_log_id) {
            1, 14, 18 => TypeBatiment::MAISON,
            2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 17, 19,
            20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33,
            34, 35, 36, 37, 38, 39, 40 => TypeBatiment::IMMEUBLE,
        };
    }

    public function annee_construction(): int
    {
        return $this->annee_construction ?? $this->periode_construction()->intval();
    }

    public function periode_construction(): PeriodeConstruction
    {
        return match ($this->enum_periode_construction_id) {
            1 => PeriodeConstruction::AVANT_1948,
            2 => PeriodeConstruction::ENTRE_1948_1974,
            3 => PeriodeConstruction::ENTRE_1975_1977,
            4 => PeriodeConstruction::ENTRE_1978_1982,
            5 => PeriodeConstruction::ENTRE_1983_1988,
            6 => PeriodeConstruction::ENTRE_1989_2000,
            7 => PeriodeConstruction::ENTRE_2001_2005,
            8 => PeriodeConstruction::ENTRE_2006_2012,
            9 => PeriodeConstruction::ENTRE_2013_2021,
            10 => PeriodeConstruction::APRES_2021,
        };
    }

    public function surface_habitable(): float
    {
        return $this->surface_habitable_immeuble ?? $this->surface_habitable_logement;
    }

    public function logements(): int
    {
        return $this->nombre_appartement ?? 1;
    }
}
