<?php

namespace App\Legacy\Model\Sortie;

final class EPConso
{
    public function __construct(
        public readonly float $ep_conso_ch,
        public readonly float $ep_conso_ch_depensier,
        public readonly float $ep_conso_ecs,
        public readonly float $ep_conso_ecs_depensier,
        public readonly float $ep_conso_eclairage,
        public readonly float $ep_conso_auxiliaire_generation_ch,
        public readonly float $ep_conso_auxiliaire_generation_ch_depensier,
        public readonly float $ep_conso_auxiliaire_distribution_ch,
        public readonly float $ep_conso_auxiliaire_generation_ecs,
        public readonly float $ep_conso_auxiliaire_generation_ecs_depensier,
        public readonly float $ep_conso_auxiliaire_distribution_ecs,
        public readonly float $ep_conso_auxiliaire_distribution_fr,
        public readonly float $ep_conso_auxiliaire_ventilation,
        public readonly float $ep_conso_totale_auxiliaire,
        public readonly float $ep_conso_fr,
        public readonly float $ep_conso_fr_depensier,
        public readonly float $ep_conso_5_usages,
        public readonly float $ep_conso_5_usages_m2
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/ep_conso
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            ep_conso_ch: (float) $xml->ep_conso_ch,
            ep_conso_ch_depensier: (float) $xml->ep_conso_ch_depensier,
            ep_conso_ecs: (float) $xml->ep_conso_ecs,
            ep_conso_ecs_depensier: (float) $xml->ep_conso_ecs_depensier,
            ep_conso_eclairage: (float) $xml->ep_conso_eclairage,
            ep_conso_auxiliaire_generation_ch: (float) $xml->ep_conso_auxiliaire_generation_ch,
            ep_conso_auxiliaire_generation_ch_depensier: (float) $xml->ep_conso_auxiliaire_generation_ch_depensier,
            ep_conso_auxiliaire_distribution_ch: (float) $xml->ep_conso_auxiliaire_distribution_ch,
            ep_conso_auxiliaire_generation_ecs: (float) $xml->ep_conso_auxiliaire_generation_ecs,
            ep_conso_auxiliaire_generation_ecs_depensier: (float) $xml->ep_conso_auxiliaire_generation_ecs_depensier,
            ep_conso_auxiliaire_distribution_ecs: (float) $xml->ep_conso_auxiliaire_distribution_ecs,
            ep_conso_auxiliaire_distribution_fr: (float) $xml->ep_conso_auxiliaire_distribution_fr,
            ep_conso_auxiliaire_ventilation: (float) $xml->ep_conso_auxiliaire_ventilation,
            ep_conso_totale_auxiliaire: (float) $xml->ep_conso_totale_auxiliaire,
            ep_conso_fr: (float) $xml->ep_conso_fr,
            ep_conso_fr_depensier: (float) $xml->ep_conso_fr_depensier,
            ep_conso_5_usages: (float) $xml->ep_conso_5_usages,
            ep_conso_5_usages_m2: (float) $xml->ep_conso_5_usages_m2
        );
    }

    public function ep_conso_auxiliaire_ch(): float
    {
        return $this->ep_conso_auxiliaire_generation_ch + $this->ep_conso_auxiliaire_distribution_ch;
    }

    public function ep_conso_auxiliaire_ecs(): float
    {
        return $this->ep_conso_auxiliaire_generation_ecs + $this->ep_conso_auxiliaire_distribution_ecs;
    }

    public function ep_conso_auxiliaire_fr(): float
    {
        return $this->ep_conso_auxiliaire_distribution_fr;
    }
}
