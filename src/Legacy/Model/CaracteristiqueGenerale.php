<?php

namespace App\Legacy\Model;

final class CaracteristiqueGenerale
{
    public function __construct(
        public readonly float $hsp,
        public readonly ?int $annee_construction,
        public readonly ?bool $appartement_non_visite,
        public readonly ?float $surface_habitable_logement,
        public readonly ?float $surface_habitable_immeuble,
        public readonly ?float $surface_tertiaire_immeuble,
        public readonly ?int $nombre_niveau_immeuble,
        public readonly ?int $nombre_niveau_logement,
        public readonly ?int $nombre_appartement,
        public readonly ?string $nom_scenario,

        public readonly int $enum_periode_construction_id,
        public readonly int $enum_methode_application_dpe_log_id,
        public readonly ?int $enum_calcul_echantillonnage_id,
        public readonly ?int $enum_scenario_id,
        public readonly ?int $enum_etape_id,
    ) {}

    /**
     * XPATH //caracteristique_generale
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            hsp: (float) $xml->hsp,
            annee_construction: (int) $xml->annee_construction ?: null,
            surface_habitable_logement: (float) $xml->surface_habitable_logement ?: null,
            surface_habitable_immeuble: (float) $xml->surface_habitable_immeuble ?: null,
            surface_tertiaire_immeuble: (float) $xml->surface_tertiaire_immeuble ?: null,
            nombre_appartement: (int) $xml->nombre_appartement ?: null,
            nombre_niveau_immeuble: (int) $xml->nombre_niveau_immeuble ?: null,
            nombre_niveau_logement: (int) $xml->nombre_niveau_logement ?: null,
            appartement_non_visite: (bool)(int) $xml->appartement_non_visite ?: null,
            nom_scenario: (string) $xml->nom_scenario ?: null,
            enum_periode_construction_id: (int) $xml->enum_periode_construction_id,
            enum_methode_application_dpe_log_id: (int) $xml->enum_methode_application_dpe_log_id,
            enum_calcul_echantillonnage_id: (int) $xml->enum_calcul_echantillonnage_id ?: null,
            enum_scenario_id: (int) $xml->enum_scenario_id ?: null,
            enum_etape_id: (int) $xml->enum_etape_id ?: null,
        );
    }
}
