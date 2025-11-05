<?php

namespace App\Legacy\Model\Sortie;

final class ProductionElectricite
{
    public function __construct(
        public readonly float $production_pv,
        public readonly float $conso_elec_ac,
        public readonly float $conso_elec_ac_ch,
        public readonly float $conso_elec_ac_ecs,
        public readonly float $conso_elec_ac_fr,
        public readonly float $conso_elec_ac_eclairage,
        public readonly float $conso_elec_ac_auxiliaire,
        public readonly float $conso_elec_ac_autre_usage
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/production_electricite
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            production_pv: (float) $xml->production_pv,
            conso_elec_ac: (float) $xml->conso_elec_ac,
            conso_elec_ac_ch: (float) $xml->conso_elec_ac_ch,
            conso_elec_ac_ecs: (float) $xml->conso_elec_ac_ecs,
            conso_elec_ac_fr: (float) $xml->conso_elec_ac_fr,
            conso_elec_ac_eclairage: (float) $xml->conso_elec_ac_eclairage,
            conso_elec_ac_auxiliaire: (float) $xml->conso_elec_ac_auxiliaire,
            conso_elec_ac_autre_usage: (float) $xml->conso_elec_ac_autre_usage
        );
    }
}
