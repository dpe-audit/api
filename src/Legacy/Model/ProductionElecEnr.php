<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

/**
 * @property array<PanneauPv> $panneaux_pv_collection
 */
final class ProductionElecEnr
{
    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly bool $presence_production_pv,
        public readonly ?int $enum_type_enr_id,
        public readonly array $panneaux_pv_collection,
        public readonly ?float $taux_autoproduction,
        public readonly float $production_pv,
        public readonly float $conso_elec_ac
    ) {}

    /**
     * XPATH logement/production_elec_enr
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        $panneaux_pv_collection = [];

        foreach ($xml->panneaux_pv_collection->panneaux_pv ?? [] as $item) {
            $panneaux_pv_collection[] = PanneauPv::from($item);
        }

        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            description: (string) $xml->donnee_entree->description ?: null,
            presence_production_pv: (bool) $xml->donnee_entree->presence_production_pv,
            enum_type_enr_id: (int) $xml->donnee_entree->enum_type_enr_id,
            taux_autoproduction: (float) $xml->donnee_intermediaire->taux_autoproduction ?: null,
            production_pv: (float) $xml->donnee_intermediaire->production_pv,
            conso_elec_ac: (float) $xml->donnee_intermediaire->conso_elec_ac,
            panneaux_pv_collection: $panneaux_pv_collection,
        );
    }
}
