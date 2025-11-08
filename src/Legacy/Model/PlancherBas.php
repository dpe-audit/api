<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class PlancherBas extends ParoiOpaque
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

        public readonly bool $calcul_ue,
        public readonly ?float $perimetre_ue,
        public readonly ?float $surface_ue,
        public readonly ?float $upb0_saisi,
        public readonly ?float $upb_saisi,

        public readonly int $enum_type_adjacence_id,
        public readonly int $enum_type_isolation_id,
        public readonly int $enum_methode_saisie_u0_id,
        public readonly int $enum_methode_saisie_u_id,
        public readonly int $enum_type_plancher_bas_id,
        public readonly ?int $enum_periode_isolation_id,
        public readonly ?int $enum_cfg_isolation_lnc_id,

        public readonly ?int $tv_upb0_id,
        public readonly int $tv_upb_id,
        public readonly ?int $tv_coef_reduction_deperdition_id,

        public readonly float $b,
        public readonly float $upb,
        public readonly float $upb_final,
        public readonly ?float $ue,
        public readonly ?float $upb0
    ) {}

    /**
     * XPATH //enveloppe/plancher_bas_collection/plancher_bas
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

            calcul_ue: (bool)(int) $xml->donnee_entree->calcul_ue,
            perimetre_ue: (float) $xml->donnee_entree->perimetre_ue ?: null,
            surface_ue: (float) $xml->donnee_entree->surface_ue ?: null,
            upb0_saisi: (float) $xml->donnee_entree->upb0_saisi ?: null,
            upb_saisi: (float) $xml->donnee_entree->upb_saisi ?: null,

            enum_type_plancher_bas_id: (int) $xml->donnee_entree->enum_type_plancher_bas_id,
            enum_type_adjacence_id: (int) $xml->donnee_entree->enum_type_adjacence_id,
            enum_methode_saisie_u0_id: (int) $xml->donnee_entree->enum_methode_saisie_u0_id,
            enum_methode_saisie_u_id: (int) $xml->donnee_entree->enum_methode_saisie_u_id,
            enum_type_isolation_id: (int) $xml->donnee_entree->enum_type_isolation_id,
            enum_cfg_isolation_lnc_id: (int) $xml->donnee_entree->enum_cfg_isolation_lnc_id ?: null,
            enum_periode_isolation_id: (int) $xml->donnee_entree->enum_periode_isolation_id ?: null,

            tv_upb0_id: (int) $xml->donnee_entree->tv_upb0_id ?: null,
            tv_upb_id: (int) $xml->donnee_entree->tv_upb_id,
            tv_coef_reduction_deperdition_id: (int) $xml->donnee_entree->tv_coef_reduction_deperdition_id ?: null,

            b: (float) $xml->donnee_intermediaire->b,
            upb: (float) $xml->donnee_intermediaire->upb,
            upb_final: (float) $xml->donnee_intermediaire->upb_final,
            ue: (float) $xml->donnee_entree->ue ?: null,
            upb0: (float) $xml->donnee_intermediaire->upb0 ?: null
        );
    }

    public function u(): float
    {
        return $this->upb_final;
    }
}
