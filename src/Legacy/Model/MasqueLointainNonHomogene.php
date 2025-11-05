<?php

namespace App\Legacy\Model;

final class MasqueLointainNonHomogene
{
    use WithId;

    public function __construct(
        public readonly int $tv_coef_masque_lointain_non_homogene_id,
    ) {}

    /**
     * XPATH //masque_lointain_non_homogene
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            tv_coef_masque_lointain_non_homogene_id: (int) $xml->donnee_entree->tv_coef_masque_lointain_non_homogene_id,
        );
    }
}
