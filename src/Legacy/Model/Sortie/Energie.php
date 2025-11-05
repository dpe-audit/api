<?php

namespace App\Legacy\Model\Sortie;

final class Energie
{
    public function __construct(
        public readonly int $enum_type_energie_id,
        public readonly float $conso_ch,
        public readonly float $conso_ecs,
        public readonly float $conso_5_usages,
        public readonly float $emission_ges_ch,
        public readonly float $emission_ges_ecs,
        public readonly float $emission_ges_5_usages,
        public readonly float $cout_ch,
        public readonly float $cout_ecs,
        public readonly float $cout_5_usages
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/sortie_par_energie_collection/sortie_par_energie
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            enum_type_energie_id: (int) $xml->enum_type_energie_id,
            conso_ch: (float) $xml->conso_ch,
            conso_ecs: (float) $xml->conso_ecs,
            conso_5_usages: (float) $xml->conso_5_usages,
            emission_ges_ch: (float) $xml->emission_ges_ch,
            emission_ges_ecs: (float) $xml->emission_ges_ecs,
            emission_ges_5_usages: (float) $xml->emission_ges_5_usages,
            cout_ch: (float) $xml->cout_ch,
            cout_ecs: (float) $xml->cout_ecs,
            cout_5_usages: (float) $xml->cout_5_usages
        );
    }

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/sortie_par_energie_collection
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->sortie_par_energie as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }
}
