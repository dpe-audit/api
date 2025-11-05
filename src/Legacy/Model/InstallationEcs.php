<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

/**
 * @property array<GenerateurEcs> $generateur_ecs_collection
 */
final class InstallationEcs
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly ?float $ratio_virtualisation,
        public readonly ?float $cle_repartition_ecs,
        public readonly float $surface_habitable,
        public readonly float $rdim,
        public readonly int $nombre_logement,
        public readonly int $nombre_niveau_installation_ecs,
        public readonly ?float $fecs_saisi,
        public readonly ?bool $reseau_distribution_isole,

        public readonly int $enum_cfg_installation_ecs_id,
        public readonly int $enum_type_installation_id,
        public readonly int $enum_methode_calcul_conso_id,
        public readonly int $enum_bouclage_reseau_ecs_id,
        public readonly ?int $enum_methode_saisie_fact_couv_sol_id,
        public readonly ?int $enum_type_installation_solaire_id,
        public readonly int $tv_rendement_distribution_ecs_id,
        public readonly ?int $tv_facteur_couverture_solaire_id,

        public readonly array $generateur_ecs_collection,

        public readonly float $rendement_distribution,
        public readonly float $besoin_ecs,
        public readonly float $besoin_ecs_depensier,
        public readonly ?float $fecs,
        public readonly ?float $production_ecs_solaire,
        public readonly float $conso_ecs,
        public readonly float $conso_ecs_depensier
    ) {}

    /**
     * XSD logement/installation_ecs_collection/installation_ecs
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        $generateur_ecs_collection = [];
        foreach ($xml->generateur_ecs_collection->generateur_ecs ?? [] as $item) {
            $generateur_ecs_collection[] = GenerateurEcs::from($item);
        }
        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            description: (string) $xml->donnee_entree->description ?: null,
            ratio_virtualisation: (float) $xml->donnee_entree->ratio_virtualisation ?: null,
            cle_repartition_ecs: (float) $xml->donnee_entree->cle_repartition_ecs ?: null,
            surface_habitable: (float) $xml->donnee_entree->surface_habitable,
            nombre_logement: (int) $xml->donnee_entree->nombre_logement,
            rdim: (float) $xml->donnee_entree->rdim,
            nombre_niveau_installation_ecs: (int) $xml->donnee_entree->nombre_niveau_installation_ecs,
            fecs_saisi: (float) $xml->donnee_entree->fecs_saisi ?: null,
            reseau_distribution_isole: (bool)(int) $xml->donnee_entree->reseau_distribution_isole ?: null,

            enum_cfg_installation_ecs_id: (int) $xml->donnee_entree->enum_cfg_installation_ecs_id,
            enum_type_installation_id: (int) $xml->donnee_entree->enum_type_installation_id,
            enum_methode_calcul_conso_id: (int) $xml->donnee_entree->enum_methode_calcul_conso_id,
            enum_bouclage_reseau_ecs_id: (int) $xml->donnee_entree->enum_bouclage_reseau_ecs_id,
            enum_methode_saisie_fact_couv_sol_id: (int) $xml->donnee_entree->enum_methode_saisie_fact_couv_sol_id ?: null,
            enum_type_installation_solaire_id: (int) $xml->donnee_entree->enum_type_installation_solaire_id ?: null,
            tv_rendement_distribution_ecs_id: (int) $xml->donnee_entree->tv_rendement_distribution_ecs_id,
            tv_facteur_couverture_solaire_id: (int) $xml->donnee_entree->tv_facteur_couverture_solaire_id ?: null,

            generateur_ecs_collection: $generateur_ecs_collection,

            rendement_distribution: (float) $xml->donnee_intermediaire->rendement_distribution,
            besoin_ecs: (float) $xml->donnee_intermediaire->besoin_ecs,
            besoin_ecs_depensier: (float) $xml->donnee_intermediaire->besoin_ecs_depensier,
            fecs: (float) $xml->donnee_intermediaire->fecs ?: null,
            production_ecs_solaire: (float) $xml->donnee_intermediaire->production_ecs_solaire ?: null,
            conso_ecs: (float) $xml->donnee_intermediaire->conso_ecs,
            conso_ecs_depensier: (float) $xml->donnee_intermediaire->conso_ecs_depensier
        );
    }

    public function fecs_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_fact_couv_sol_id) {
            2 => $this->fecs,
            default => null,
        };
    }
}
