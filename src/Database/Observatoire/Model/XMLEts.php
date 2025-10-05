<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Enveloppe\Lnc\Baie\Materiau;
use App\Domain\Enveloppe\Lnc\Baie\TypeVitrage;

/**
 * @property array<XMLEtsBaie> $ets_baie_collection
 */
final class XMLEts
{
    use WithReferences, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly ?int $tv_coef_reduction_deperdition_id,
        public readonly ?int $enum_cfg_isolation_lnc_id,
        public readonly ?int $tv_coef_transparence_ets_id,
        public readonly array $ets_baie_collection,

        public readonly float $coef_transparence_ets,
        public readonly float $bver
    ) {}

    /**
     * XSD logement/enveloppe/ets_collection/ets
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: (string) $xml->donnee_entree->reference,
            description: (string) $xml->donnee_entree->description ?: null,
            tv_coef_reduction_deperdition_id: (int) $xml->donnee_entree->tv_coef_reduction_deperdition_id ?: null,
            enum_cfg_isolation_lnc_id: (int) $xml->donnee_entree->enum_cfg_isolation_lnc_id,
            tv_coef_transparence_ets_id: (int) $xml->donnee_entree->tv_coef_transparence_ets_id,
            ets_baie_collection: XMLEtsBaie::from_collection($xml->ets_baie_collection),
            coef_transparence_ets: (float) $xml->donnee_intermediaire->coef_transparence_ets,
            bver: (float) $xml->donnee_intermediaire->bver
        );
    }

    /**
     * XSD logement/enveloppe/ets_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->ets as $item) {
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

    public function type_vitrage(): TypeVitrage
    {
        return match ($this->tv_coef_transparence_ets_id) {
            1 => TypeVitrage::POLYCARBONATE,
            2, 7, 12, 17 => TypeVitrage::SIMPLE_VITRAGE,
            3, 8, 13, 18 => TypeVitrage::DOUBLE_VITRAGE,
            4, 9, 14, 19 => TypeVitrage::DOUBLE_VITRAGE_FE,
            5, 10, 15, 20 => TypeVitrage::TRIPLE_VITRAGE,
            6, 11, 16, 21 => TypeVitrage::TRIPLE_VITRAGE_FE,
            default => TypeVitrage::SIMPLE_VITRAGE,
        };
    }

    public function materiau(): ?Materiau
    {
        return match ($this->tv_coef_transparence_ets_id) {
            1 => Materiau::POLYCARBONATE,
            2, 3, 4, 5, 6 => Materiau::BOIS,
            7, 8, 9, 10, 11 => Materiau::PVC,
            12, 13, 14, 15, 16, 17, 18, 19, 20, 21 => Materiau::METAL,
            default => null,
        };
    }

    public function presence_rupteur_pont_thermique(): ?bool
    {
        return match ($this->tv_coef_transparence_ets_id) {
            12, 13, 14, 15, 16 => true,
            17, 18, 19, 20, 21 => false,
            default => null,
        };
    }
}
