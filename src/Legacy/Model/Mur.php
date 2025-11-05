<?php

namespace App\Legacy\Model;

final class Mur extends ParoiOpaque
{
    public function __construct(
        public readonly bool $enduit_isolant_paroi_ancienne,
        public readonly ?float $surface_paroi_totale,
        public readonly ?float $epaisseur_structure,
        public readonly ?float $umur0_saisi,
        public readonly ?float $umur_saisi,

        public readonly int $enum_methode_saisie_u_id,
        public readonly int $enum_orientation_id,
        public readonly int $enum_materiaux_structure_mur_id,
        public readonly int $enum_type_doublage_id,
        public readonly ?int $tv_umur_id,
        public readonly ?int $tv_umur0_id,

        public readonly float $umur,
        public readonly ?float $umur0,
    ) {}

    /**
     * XPATH //enveloppe/mur_collection/mur
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return (new self(
            enduit_isolant_paroi_ancienne: (bool) $xml->donnee_entree->enduit_isolant_paroi_ancienne,
            surface_paroi_totale: (float) $xml->donnee_entree->surface_paroi_totale ?: null,
            epaisseur_structure: (float) $xml->donnee_entree->epaisseur_structure ?: null,
            umur0_saisi: (float) $xml->donnee_entree->umur0_saisi ?: null,
            umur_saisi: (float) $xml->donnee_entree->umur_saisi ?: null,
            enum_methode_saisie_u_id: (int) $xml->donnee_entree->enum_methode_saisie_u_id,
            enum_orientation_id: (int) $xml->donnee_entree->enum_orientation_id,
            enum_materiaux_structure_mur_id: (int) $xml->donnee_entree->enum_materiaux_structure_mur_id,
            enum_type_doublage_id: (int) $xml->donnee_entree->enum_type_doublage_id,
            tv_umur0_id: (int) $xml->donnee_entree->tv_umur0_id ?: null,
            tv_umur_id: (int) $xml->donnee_entree->tv_umur_id ?: null,
            umur: (float) $xml->donnee_intermediaire->umur,
            umur0: (float) $xml->donnee_intermediaire->umur0 ?: null
        ))->set($xml);
    }
}
