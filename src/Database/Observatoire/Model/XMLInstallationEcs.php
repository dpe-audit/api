<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Ecs\Installation\Solaire\Usage;
use App\Domain\Ecs\Systeme\Reseau\BouclageReseau;
use App\Domain\Ecs\Systeme\Reseau\IsolationReseau;

/**
 * @property array<XMLGenerateurEcs> $generateur_ecs_collection
 */
final class XMLInstallationEcs
{
    use WithDescription, WithReferences;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly int $enum_cfg_installation_ecs_id,
        public readonly int $enum_type_installation_id,
        public readonly int $enum_methode_calcul_conso_id,
        public readonly ?float $ratio_virtualisation,
        public readonly ?float $cle_repartition_ecs,
        public readonly float $surface_habitable,
        public readonly int $nombre_logement,
        public readonly float $rdim,
        public readonly int $nombre_niveau_installation_ecs,
        public readonly ?float $fecs_saisi,
        public readonly ?int $tv_facteur_couverture_solaire_id,
        public readonly ?int $enum_methode_saisie_fact_couv_sol_id,
        public readonly ?int $enum_type_installation_solaire_id,
        public readonly int $tv_rendement_distribution_ecs_id,
        public readonly int $enum_bouclage_reseau_ecs_id,
        public readonly ?bool $reseau_distribution_isole,
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
        return new self(
            reference: (string) $xml->donnee_entree->reference,
            description: (string) $xml->donnee_entree->description ?: null,
            enum_cfg_installation_ecs_id: (int) $xml->donnee_entree->enum_cfg_installation_ecs_id,
            enum_type_installation_id: (int) $xml->donnee_entree->enum_type_installation_id,
            enum_methode_calcul_conso_id: (int) $xml->donnee_entree->enum_methode_calcul_conso_id,
            ratio_virtualisation: (float) $xml->donnee_entree->ratio_virtualisation ?: null,
            cle_repartition_ecs: (float) $xml->donnee_entree->cle_repartition_ecs ?: null,
            surface_habitable: (float) $xml->donnee_entree->surface_habitable,
            nombre_logement: (int) $xml->donnee_entree->nombre_logement,
            rdim: (float) $xml->donnee_entree->rdim,
            nombre_niveau_installation_ecs: (int) $xml->donnee_entree->nombre_niveau_installation_ecs,
            fecs_saisi: (float) $xml->donnee_entree->fecs_saisi ?: null,
            tv_facteur_couverture_solaire_id: (int) $xml->donnee_entree->tv_facteur_couverture_solaire_id ?: null,
            enum_methode_saisie_fact_couv_sol_id: (int) $xml->donnee_entree->enum_methode_saisie_fact_couv_sol_id ?: null,
            enum_type_installation_solaire_id: (int) $xml->donnee_entree->enum_type_installation_solaire_id ?: null,
            tv_rendement_distribution_ecs_id: (int) $xml->donnee_entree->tv_rendement_distribution_ecs_id,
            enum_bouclage_reseau_ecs_id: (int) $xml->donnee_entree->enum_bouclage_reseau_ecs_id,
            reseau_distribution_isole: (bool)(int) $xml->donnee_entree->reseau_distribution_isole ?: null,
            generateur_ecs_collection: XMLGenerateurEcs::from_collection($xml->generateur_ecs_collection),
            rendement_distribution: (float) $xml->donnee_intermediaire->rendement_distribution,
            besoin_ecs: (float) $xml->donnee_intermediaire->besoin_ecs,
            besoin_ecs_depensier: (float) $xml->donnee_intermediaire->besoin_ecs_depensier,
            fecs: (float) $xml->donnee_intermediaire->fecs ?: null,
            production_ecs_solaire: (float) $xml->donnee_intermediaire->production_ecs_solaire ?: null,
            conso_ecs: (float) $xml->donnee_intermediaire->conso_ecs,
            conso_ecs_depensier: (float) $xml->donnee_intermediaire->conso_ecs_depensier
        );
    }

    /**
     * XSD logement/installation_ecs_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->installation_ecs as $item) {
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
        return $this->description ?? 'Non renseignée';
    }

    public function surface(): float
    {
        return $this->surface_habitable;
    }

    public function alimentation_contigues(): bool
    {
        return match ($this->tv_rendement_distribution_ecs_id) {
            1, 4, 6 => true,
            default => false,
        };
    }

    public function niveaux_desservis(): int
    {
        return $this->nombre_niveau_installation_ecs;
    }

    public function isolation_reseau(): ?IsolationReseau
    {
        return match ($this->reseau_distribution_isole) {
            true => IsolationReseau::ISOLE,
            false => IsolationReseau::NON_ISOLE,
            default => null,
        };
    }

    public function bouclage_reseau(): ?BouclageReseau
    {
        return match ($this->enum_bouclage_reseau_ecs_id) {
            1 => BouclageReseau::RESEAU_NON_BOUCLE,
            2 => BouclageReseau::RESEAU_BOUCLE,
            3 => BouclageReseau::RESEAU_TRACE,
            default => null,
        };
    }

    public function usage_solaire(): ?Usage
    {
        return match ($this->enum_type_installation_solaire_id) {
            2, 3 => Usage::ECS,
            4 => Usage::CHAUFFAGE_ECS,
            default => null,
        };
    }

    public function annee_installation_solaire(XMLRessource $ressource): ?int
    {
        return match ($this->enum_type_installation_solaire_id) {
            3 => $ressource->administratif->annee_etablissement(),
            default => null,
        };
    }

    public function installation_collective(): bool
    {
        return \in_array($this->enum_type_installation_id, [2, 3, 4]);
    }

    public function fecs_saisi(): ?float
    {
        return match($this->enum_methode_saisie_fact_couv_sol_id) {
            2 => $this->fecs,
            default => null,
        };
    }
}
