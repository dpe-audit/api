<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

/**
 * @property array<EtsBaie> $ets_baie_collection
 */
final class Ets
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly ?int $enum_cfg_isolation_lnc_id,
        public readonly ?int $tv_coef_reduction_deperdition_id,
        public readonly ?int $tv_coef_transparence_ets_id,
        public readonly array $ets_baie_collection,
        public readonly float $coef_transparence_ets,
        public readonly float $bver
    ) {}

    /**
     * XPATH //enveloppe/ets_collection/ets
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        $ets_baie_collection = [];
        foreach ($xml->baie_ets as $item) {
            $ets_baie_collection[] = EtsBaie::from($item);
        }

        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            description: (string) $xml->donnee_entree->description ?: null,
            enum_cfg_isolation_lnc_id: (int) $xml->donnee_entree->enum_cfg_isolation_lnc_id,
            tv_coef_transparence_ets_id: (int) $xml->donnee_entree->tv_coef_transparence_ets_id,
            tv_coef_reduction_deperdition_id: (int) $xml->donnee_entree->tv_coef_reduction_deperdition_id ?: null,
            ets_baie_collection: $ets_baie_collection,
            coef_transparence_ets: (float) $xml->donnee_intermediaire->coef_transparence_ets,
            bver: (float) $xml->donnee_intermediaire->bver
        );
    }

    public function match_reference(string $reference): bool
    {
        return $this->reference === $reference || str_contains($this->reference, $reference);
    }
}
