<?php

namespace App\Legacy\Model\Sortie;

final class Deperdition
{
    public function __construct(
        public readonly float $hvent,
        public readonly float $hperm,
        public readonly float $deperdition_renouvellement_air,
        public readonly float $deperdition_mur,
        public readonly float $deperdition_plancher_bas,
        public readonly float $deperdition_plancher_haut,
        public readonly float $deperdition_baie_vitree,
        public readonly float $deperdition_porte,
        public readonly float $deperdition_pont_thermique,
        public readonly float $deperdition_enveloppe,
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/deperdition
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            hvent: (float) $xml->hvent,
            hperm: (float) $xml->hperm,
            deperdition_renouvellement_air: (float) $xml->deperdition_renouvellement_air,
            deperdition_mur: (float) $xml->deperdition_mur,
            deperdition_plancher_bas: (float) $xml->deperdition_plancher_bas,
            deperdition_plancher_haut: (float) $xml->deperdition_plancher_haut,
            deperdition_baie_vitree: (float) $xml->deperdition_baie_vitree,
            deperdition_porte: (float) $xml->deperdition_porte,
            deperdition_pont_thermique: (float) $xml->deperdition_pont_thermique,
            deperdition_enveloppe: (float) $xml->deperdition_enveloppe
        );
    }

    public function gv(): float
    {
        return $this->deperdition_enveloppe;
    }

    public function dp(): float
    {
        return $this->deperdition_mur
            + $this->deperdition_plancher_bas
            + $this->deperdition_plancher_haut
            + $this->deperdition_baie_vitree
            + $this->deperdition_porte;
    }

    public function pt(): float
    {
        return $this->deperdition_pont_thermique;
    }

    public function dr(): float
    {
        return $this->deperdition_renouvellement_air;
    }
}
