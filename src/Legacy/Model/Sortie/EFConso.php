<?php

namespace App\Legacy\Model\Sortie;

final class EFConso
{
    public function __construct(
        public readonly float $conso_ch,
        public readonly float $conso_ch_depensier,
        public readonly float $conso_ecs,
        public readonly float $conso_ecs_depensier,
        public readonly float $conso_eclairage,
        public readonly float $conso_auxiliaire_generation_ch,
        public readonly float $conso_auxiliaire_generation_ch_depensier,
        public readonly float $conso_auxiliaire_distribution_ch,
        public readonly float $conso_auxiliaire_generation_ecs,
        public readonly float $conso_auxiliaire_generation_ecs_depensier,
        public readonly float $conso_auxiliaire_distribution_ecs,
        public readonly float $conso_auxiliaire_distribution_fr,
        public readonly float $conso_auxiliaire_ventilation,
        public readonly float $conso_totale_auxiliaire,
        public readonly float $conso_fr,
        public readonly float $conso_fr_depensier,
        public readonly float $conso_5_usages,
        public readonly float $conso_5_usages_m2
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/ef_conso
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            conso_ch: (float) $xml->conso_ch,
            conso_ch_depensier: (float) $xml->conso_ch_depensier,
            conso_ecs: (float) $xml->conso_ecs,
            conso_ecs_depensier: (float) $xml->conso_ecs_depensier,
            conso_eclairage: (float) $xml->conso_eclairage,
            conso_auxiliaire_generation_ch: (float) $xml->conso_auxiliaire_generation_ch,
            conso_auxiliaire_generation_ch_depensier: (float) $xml->conso_auxiliaire_generation_ch_depensier,
            conso_auxiliaire_distribution_ch: (float) $xml->conso_auxiliaire_distribution_ch,
            conso_auxiliaire_generation_ecs: (float) $xml->conso_auxiliaire_generation_ecs,
            conso_auxiliaire_generation_ecs_depensier: (float) $xml->conso_auxiliaire_generation_ecs_depensier,
            conso_auxiliaire_distribution_ecs: (float) $xml->conso_auxiliaire_distribution_ecs,
            conso_auxiliaire_distribution_fr: (float) $xml->conso_auxiliaire_distribution_fr,
            conso_auxiliaire_ventilation: (float) $xml->conso_auxiliaire_ventilation,
            conso_totale_auxiliaire: (float) $xml->conso_totale_auxiliaire,
            conso_fr: (float) $xml->conso_fr,
            conso_fr_depensier: (float) $xml->conso_fr_depensier,
            conso_5_usages: (float) $xml->conso_5_usages,
            conso_5_usages_m2: (float) $xml->conso_5_usages_m2
        );
    }

    public function conso_auxiliaire_ch(): float
    {
        return $this->conso_auxiliaire_generation_ch + $this->conso_auxiliaire_distribution_ch;
    }

    public function conso_auxiliaire_ecs(): float
    {
        return $this->conso_auxiliaire_generation_ecs + $this->conso_auxiliaire_distribution_ecs;
    }

    public function conso_auxiliaire_fr(): float
    {
        return $this->conso_auxiliaire_distribution_fr;
    }
}
