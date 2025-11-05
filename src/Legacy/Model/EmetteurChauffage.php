<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class EmetteurChauffage
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly float $surface_chauffee,
        public readonly ?bool $reseau_distribution_isole,

        public readonly int $enum_equipement_intermittence_id,
        public readonly int $enum_type_regulation_id,
        public readonly int $enum_type_chauffage_id,
        public readonly int $enum_temp_distribution_ch_id,
        public readonly int $enum_lien_generateur_emetteur_id,
        public readonly int $enum_type_emission_distribution_id,
        public readonly ?int $enum_periode_installation_emetteur_id,

        public readonly int $tv_rendement_emission_id,
        public readonly int $tv_rendement_distribution_ch_id,
        public readonly int $tv_rendement_regulation_id,
        public readonly int $tv_intermittence_id,

        public readonly float $i0,
        public readonly float $rendement_emission,
        public readonly float $rendement_distribution,
        public readonly float $rendement_regulation,
    ) {}

    /**
     * XPATH //installation_chauffage/emetteur_chauffage_collection/emetteur_chauffage
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            description: (string) $xml->donnee_entree->description ?: null,
            surface_chauffee: (float) $xml->donnee_entree->surface_chauffee,
            reseau_distribution_isole: (bool)(int) $xml->donnee_entree->reseau_distribution_isole ?: null,
            enum_type_emission_distribution_id: (int) $xml->donnee_entree->enum_type_emission_distribution_id,
            enum_equipement_intermittence_id: (int) $xml->donnee_entree->enum_equipement_intermittence_id,
            enum_type_regulation_id: (int) $xml->donnee_entree->enum_type_regulation_id,
            enum_type_chauffage_id: (int) $xml->donnee_entree->enum_type_chauffage_id,
            enum_temp_distribution_ch_id: (int) $xml->donnee_entree->enum_temp_distribution_ch_id,
            enum_lien_generateur_emetteur_id: (int) $xml->donnee_entree->enum_lien_generateur_emetteur_id,
            enum_periode_installation_emetteur_id: (int) $xml->donnee_entree->enum_periode_installation_emetteur_id ?: null,
            tv_intermittence_id: (int) $xml->donnee_entree->tv_intermittence_id,
            tv_rendement_emission_id: (int) $xml->donnee_entree->tv_rendement_emission_id,
            tv_rendement_distribution_ch_id: (int) $xml->donnee_entree->tv_rendement_distribution_ch_id,
            tv_rendement_regulation_id: (int) $xml->donnee_entree->tv_rendement_regulation_id,
            i0: (float) $xml->donnee_intermediaire->i0,
            rendement_emission: (float) $xml->donnee_intermediaire->rendement_emission,
            rendement_distribution: (float) $xml->donnee_intermediaire->rendement_distribution,
            rendement_regulation: (float) $xml->donnee_intermediaire->rendement_regulation
        );
    }
}
