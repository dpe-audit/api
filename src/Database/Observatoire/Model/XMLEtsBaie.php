<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Enveloppe\Lnc\Baie\Mitoyennete;

final class XMLEtsBaie
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly int $enum_orientation_id,
        public readonly int $enum_inclinaison_vitrage_id,
        public readonly float $surface_totale_baie,
        public readonly int $nb_baie,
    ) {}

    /**
     * XSD logement/enveloppe/ets_collection/ets/baie_ets_collection/baie_ets
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: (string) $xml->donnee_entree->reference,
            description: (string) $xml->donnee_entree->description ?: null,
            enum_orientation_id: (int) $xml->donnee_entree->enum_orientation_id,
            enum_inclinaison_vitrage_id: (int) $xml->donnee_entree->enum_inclinaison_vitrage_id,
            surface_totale_baie: (float) $xml->donnee_entree->surface_totale_baie,
            nb_baie: (int) $xml->donnee_entree->nb_baie
        );
    }

    /**
     * XSD logement/enveloppe/ets_collection/ets/baie_ets_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->baie_ets as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function identifiers(): array
    {
        return [$this->reference];
    }

    public function description(): string
    {
        return $this->description ?? 'Description non renseignée';
    }

    public function mitoyennete(): Mitoyennete
    {
        return Mitoyennete::EXTERIEUR;
    }

    public function orientation(): float
    {
        return match ($this->enum_orientation_id) {
            1 => 180,
            2 => 0,
            3 => 90,
            4 => 270,
        };
    }

    public function inclinaison(): float
    {
        return match ($this->enum_inclinaison_vitrage_id) {
            1 => 15,
            2 => 50,
            3 => 90,
            4 => 0,
        };
    }

    public function surface(): float
    {
        return $this->nb_baie ? $this->surface_totale_baie / $this->nb_baie : 0;
    }
}
