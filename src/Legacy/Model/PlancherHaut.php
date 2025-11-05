<?php

namespace App\Legacy\Model;

final class PlancherHaut extends ParoiOpaque
{
    public function __construct(
        public readonly ?float $uph0_saisi,
        public readonly ?float $uph_saisi,
        public readonly ?int $enum_type_plancher_haut_id,
        public readonly ?int $tv_uph0_id,
        public readonly int $tv_uph_id,
        public readonly float $uph,
        public readonly float $uph0
    ) {}

    /**
     * XPATH //enveloppe/plancher_haut_collection/plancher_haut
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return (new self(
            uph0_saisi: (float) $xml->donnee_entree->uph0_saisi ?: null,
            uph_saisi: (float) $xml->donnee_entree->uph_saisi ?: null,
            enum_type_plancher_haut_id: (int) $xml->donnee_entree->enum_type_plancher_haut_id,
            tv_uph0_id: (int) $xml->donnee_entree->tv_uph0_id ?: null,
            tv_uph_id: (int) $xml->donnee_entree->tv_uph_id,
            uph: (float) $xml->donnee_intermediaire->uph,
            uph0: (float) $xml->donnee_intermediaire->uph0 ?: null
        ))->set($xml);
    }
}
