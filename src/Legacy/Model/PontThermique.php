<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class PontThermique
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $reference_1,
        public readonly ?string $reference_2,
        public readonly ?string $description,
        public readonly float $pourcentage_valeur_pont_thermique,
        public readonly float $l,
        public readonly ?float $k_saisi,
        public readonly int $enum_type_liaison_id,
        public readonly int $enum_methode_saisie_pont_thermique_id,
        public readonly ?int $tv_pont_thermique_id,
        public readonly float $k
    ) {}

    /**
     * XPATH logement/enveloppe/pont_thermique_collection/pont_thermique
     * XPATH logement[caracteristique_generale/enum_scenario_id="0"]/enveloppe/pont_thermique_collection/pont_thermique
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            reference_1: Normalizer::referenceval((string) $xml->donnee_entree->reference_1),
            reference_2: Normalizer::referenceval((string) $xml->donnee_entree->reference_2),
            description: (string) $xml->donnee_entree->description ?: null,
            pourcentage_valeur_pont_thermique: (float) $xml->donnee_entree->pourcentage_valeur_pont_thermique,
            l: (float) $xml->donnee_entree->l,
            k_saisi: (float) $xml->donnee_entree->k_saisi ?: null,
            enum_type_liaison_id: (int) $xml->donnee_entree->enum_type_liaison_id,
            enum_methode_saisie_pont_thermique_id: (int) $xml->donnee_entree->enum_methode_saisie_pont_thermique_id,
            tv_pont_thermique_id: (int) $xml->donnee_entree->tv_pont_thermique_id ?: null,
            k: (float) $xml->donnee_intermediaire->k
        );
    }

    public function pt(): float
    {
        return $this->k * $this->pourcentage_valeur_pont_thermique * $this->l;
    }
}
