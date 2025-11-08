<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class PlancherHaut extends ParoiOpaque
{
    public function __construct(
        public readonly string $reference,
        public readonly ?string $reference_lnc,
        public readonly ?string $description,
        public readonly ?float $surface_aiu,
        public readonly ?float $surface_aue,
        public readonly float $surface_paroi_opaque,
        public readonly ?float $resistance_isolation,
        public readonly ?float $epaisseur_isolation,
        public readonly ?bool $paroi_lourde,
        public readonly ?float $uph0_saisi,
        public readonly ?float $uph_saisi,

        public readonly int $enum_type_adjacence_id,
        public readonly int $enum_type_isolation_id,
        public readonly int $enum_methode_saisie_u0_id,
        public readonly int $enum_methode_saisie_u_id,
        public readonly ?int $enum_type_plancher_haut_id,
        public readonly ?int $enum_periode_isolation_id,
        public readonly ?int $enum_cfg_isolation_lnc_id,

        public readonly ?int $tv_uph0_id,
        public readonly int $tv_uph_id,
        public readonly ?int $tv_coef_reduction_deperdition_id,

        public readonly float $b,
        public readonly float $uph,
        public readonly ?float $uph0
    ) {}

    /**
     * XPATH //enveloppe/plancher_haut_collection/plancher_haut
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            reference_lnc: Normalizer::referenceval((string) $xml->donnee_entree->reference_lnc),
            description: (string) $xml->donnee_entree->description ?: null,
            surface_aiu: (float) $xml->donnee_entree->surface_aiu ?: null,
            surface_aue: (float) $xml->donnee_entree->surface_aue ?: null,
            surface_paroi_opaque: (float) $xml->donnee_entree->surface_paroi_opaque,
            paroi_lourde: (bool)(int) $xml->donnee_entree->paroi_lourde ?: null,
            resistance_isolation: (float) $xml->donnee_entree->resistance_isolation ?: null,
            epaisseur_isolation: (float) $xml->donnee_entree->epaisseur_isolation ?: null,

            uph0_saisi: (float) $xml->donnee_entree->uph0_saisi ?: null,
            uph_saisi: (float) $xml->donnee_entree->uph_saisi ?: null,

            enum_type_plancher_haut_id: (int) $xml->donnee_entree->enum_type_plancher_haut_id,
            enum_type_adjacence_id: (int) $xml->donnee_entree->enum_type_adjacence_id,
            enum_methode_saisie_u0_id: (int) $xml->donnee_entree->enum_methode_saisie_u0_id,
            enum_methode_saisie_u_id: (int) $xml->donnee_entree->enum_methode_saisie_u_id,
            enum_type_isolation_id: (int) $xml->donnee_entree->enum_type_isolation_id,
            enum_cfg_isolation_lnc_id: (int) $xml->donnee_entree->enum_cfg_isolation_lnc_id ?: null,
            enum_periode_isolation_id: (int) $xml->donnee_entree->enum_periode_isolation_id ?: null,

            tv_uph0_id: (int) $xml->donnee_entree->tv_uph0_id ?: null,
            tv_uph_id: (int) $xml->donnee_entree->tv_uph_id,
            tv_coef_reduction_deperdition_id: (int) $xml->donnee_entree->tv_coef_reduction_deperdition_id ?: null,

            b: (float) $xml->donnee_intermediaire->b,
            uph: (float) $xml->donnee_intermediaire->uph,
            uph0: (float) $xml->donnee_intermediaire->uph0 ?: null
        );
    }

    public function u(): float
    {
        return $this->uph;
    }
}
