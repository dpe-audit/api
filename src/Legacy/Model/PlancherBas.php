<?php

namespace App\Legacy\Model;

final class PlancherBas extends ParoiOpaque
{
    public function __construct(
        public readonly bool $calcul_ue,
        public readonly ?float $perimetre_ue,
        public readonly ?float $surface_ue,
        public readonly ?float $upb0_saisi,
        public readonly ?float $upb_saisi,
        public readonly int $enum_type_plancher_bas_id,
        public readonly ?int $tv_upb0_id,
        public readonly int $tv_upb_id,
        public readonly ?float $ue,
        public readonly float $upb,
        public readonly float $upb_final,
        public readonly float $upb0
    ) {}

    /**
     * XPATH //enveloppe/plancher_bas_collection/plancher_bas
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return (new self(
            calcul_ue: (bool)(int) $xml->donnee_entree->calcul_ue,
            perimetre_ue: (float) $xml->donnee_entree->perimetre_ue ?: null,
            surface_ue: (float) $xml->donnee_entree->surface_ue ?: null,
            upb0_saisi: (float) $xml->donnee_entree->upb0_saisi ?: null,
            upb_saisi: (float) $xml->donnee_entree->upb_saisi ?: null,
            enum_type_plancher_bas_id: (int) $xml->donnee_entree->enum_type_plancher_bas_id,
            tv_upb0_id: (int) $xml->donnee_entree->tv_upb0_id ?: null,
            tv_upb_id: (int) $xml->donnee_entree->tv_upb_id,
            ue: (float) $xml->donnee_entree->ue ?: null,
            upb: (float) $xml->donnee_intermediaire->upb,
            upb_final: (float) $xml->donnee_intermediaire->upb_final,
            upb0: (float) $xml->donnee_intermediaire->upb0 ?: null
        ))->set($xml);
    }
}
