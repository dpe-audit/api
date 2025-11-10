<?php

namespace App\Legacy\Model\Sortie;

final class EmissionGES
{
    public function __construct(
        public readonly float $emission_ges_ch,
        public readonly float $emission_ges_ch_depensier,
        public readonly float $emission_ges_ecs,
        public readonly float $emission_ges_ecs_depensier,
        public readonly float $emission_ges_eclairage,
        public readonly float $emission_ges_auxiliaire_generation_ch,
        public readonly float $emission_ges_auxiliaire_generation_ch_depensier,
        public readonly float $emission_ges_auxiliaire_distribution_ch,
        public readonly float $emission_ges_auxiliaire_generation_ecs,
        public readonly float $emission_ges_auxiliaire_generation_ecs_depensier,
        public readonly float $emission_ges_auxiliaire_distribution_ecs,
        public readonly float $emission_ges_auxiliaire_distribution_fr,
        public readonly float $emission_ges_auxiliaire_ventilation,
        public readonly float $emission_ges_totale_auxiliaire,
        public readonly float $emission_ges_fr,
        public readonly float $emission_ges_fr_depensier,
        public readonly float $emission_ges_5_usages,
        public readonly float $emission_ges_5_usages_m2,
        public readonly string $classe_emission_ges
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/emission_ges
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            emission_ges_ch: (float) $xml->emission_ges_ch,
            emission_ges_ch_depensier: (float) $xml->emission_ges_ch_depensier,
            emission_ges_ecs: (float) $xml->emission_ges_ecs,
            emission_ges_ecs_depensier: (float) $xml->emission_ges_ecs_depensier,
            emission_ges_eclairage: (float) $xml->emission_ges_eclairage,
            emission_ges_auxiliaire_generation_ch: (float) $xml->emission_ges_auxiliaire_generation_ch,
            emission_ges_auxiliaire_generation_ch_depensier: (float) $xml->emission_ges_auxiliaire_generation_ch_depensier,
            emission_ges_auxiliaire_distribution_ch: (float) $xml->emission_ges_auxiliaire_distribution_ch,
            emission_ges_auxiliaire_generation_ecs: (float) $xml->emission_ges_auxiliaire_generation_ecs,
            emission_ges_auxiliaire_generation_ecs_depensier: (float) $xml->emission_ges_auxiliaire_generation_ecs_depensier,
            emission_ges_auxiliaire_distribution_ecs: (float) $xml->emission_ges_auxiliaire_distribution_ecs,
            emission_ges_auxiliaire_distribution_fr: (float) $xml->emission_ges_auxiliaire_distribution_fr,
            emission_ges_auxiliaire_ventilation: (float) $xml->emission_ges_auxiliaire_ventilation,
            emission_ges_totale_auxiliaire: (float) $xml->emission_ges_totale_auxiliaire,
            emission_ges_fr: (float) $xml->emission_ges_fr,
            emission_ges_fr_depensier: (float) $xml->emission_ges_fr_depensier,
            emission_ges_5_usages: (float) $xml->emission_ges_5_usages,
            emission_ges_5_usages_m2: (float) $xml->emission_ges_5_usages_m2,
            classe_emission_ges: (string) $xml->classe_emission_ges
        );
    }

    public function emission_ges_auxiliaire_ch(): float
    {
        return $this->emission_ges_auxiliaire_generation_ch + $this->emission_ges_auxiliaire_distribution_ch;
    }

    public function emission_ges_auxiliaire_ecs(): float
    {
        return $this->emission_ges_auxiliaire_generation_ecs + $this->emission_ges_auxiliaire_distribution_ecs;
    }

    public function emission_ges_auxiliaire_fr(): float
    {
        return $this->emission_ges_auxiliaire_distribution_fr;
    }
}
