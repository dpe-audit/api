<?php

namespace App\Legacy\Model\Sortie;

final class Cout
{
    public function __construct(
        public readonly float $cout_ch,
        public readonly float $cout_ch_depensier,
        public readonly float $cout_ecs,
        public readonly float $cout_ecs_depensier,
        public readonly float $cout_eclairage,
        public readonly float $cout_auxiliaire_ventilation,
        public readonly float $cout_total_auxiliaire,
        public readonly float $cout_fr,
        public readonly float $cout_fr_depensier,
        public readonly float $cout_5_usages
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/cout
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            cout_ch: (float) $xml->cout_ch,
            cout_ch_depensier: (float) $xml->cout_ch_depensier,
            cout_ecs: (float) $xml->cout_ecs,
            cout_ecs_depensier: (float) $xml->cout_ecs_depensier,
            cout_eclairage: (float) $xml->cout_eclairage,
            cout_auxiliaire_ventilation: (float) $xml->cout_auxiliaire_ventilation,
            cout_total_auxiliaire: (float) $xml->cout_total_auxiliaire,
            cout_fr: (float) $xml->cout_fr,
            cout_fr_depensier: (float) $xml->cout_fr_depensier,
            cout_5_usages: (float) $xml->cout_5_usages
        );
    }
}
