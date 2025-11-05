<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class EtsBaie
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly float $surface_totale_baie,
        public readonly int $nb_baie,
        public readonly int $enum_orientation_id,
        public readonly int $enum_inclinaison_vitrage_id,
    ) {}

    /**
     * XPATH //enveloppe/ets_collection/ets/baie_ets_collection/baie_ets
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            description: (string) $xml->donnee_entree->description ?: null,
            surface_totale_baie: (float) $xml->donnee_entree->surface_totale_baie,
            nb_baie: (int) $xml->donnee_entree->nb_baie,
            enum_orientation_id: (int) $xml->donnee_entree->enum_orientation_id,
            enum_inclinaison_vitrage_id: (int) $xml->donnee_entree->enum_inclinaison_vitrage_id,
        );
    }

    public function surface(): float
    {
        return $this->nb_baie ? $this->surface_totale_baie / $this->nb_baie : 0;
    }
}
