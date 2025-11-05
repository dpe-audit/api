<?php

namespace App\Legacy\Model\Sortie;

final class QualiteIsolation
{
    public function __construct(
        public readonly float $ubat,
        public readonly int $qualite_isol_enveloppe,
        public readonly int $qualite_isol_mur,
        public readonly ?int $qualite_isol_plancher_haut_toit_terrasse,
        public readonly ?int $qualite_isol_plancher_haut_comble_perdu,
        public readonly ?int $qualite_isol_plancher_haut_comble_amenage,
        public readonly ?int $qualite_isol_plancher_bas,
        public readonly int $qualite_isol_menuiserie
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/qualite_isolation
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            ubat: (float) $xml->ubat,
            qualite_isol_enveloppe: (int) $xml->qualite_isol_enveloppe,
            qualite_isol_mur: (int) $xml->qualite_isol_mur,
            qualite_isol_plancher_haut_toit_terrasse: (int) $xml->qualite_isol_plancher_haut_toit_terrasse ?: null,
            qualite_isol_plancher_haut_comble_perdu: (int) $xml->qualite_isol_plancher_haut_comble_perdu ?: null,
            qualite_isol_plancher_haut_comble_amenage: (int) $xml->qualite_isol_plancher_haut_comble_amenage ?: null,
            qualite_isol_plancher_bas: (int) $xml->qualite_isol_plancher_bas ?: null,
            qualite_isol_menuiserie: (int) $xml->qualite_isol_menuiserie
        );
    }
}
