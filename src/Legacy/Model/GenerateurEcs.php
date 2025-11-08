<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class GenerateurEcs
{
    use WithId, WithDescription;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $reference_generateur_mixte,
        public readonly ?string $description,
        public readonly ?string $ref_produit_generateur_ecs,
        public readonly ?string $identifiant_reseau_chaleur,
        public readonly ?string $date_arrete_reseau_chaleur,
        public readonly bool $position_volume_chauffe,
        public readonly ?bool $position_volume_chauffe_stockage,
        public readonly float $volume_stockage,
        public readonly ?bool $presence_ventouse,

        public readonly int $enum_methode_saisie_carac_sys_id,
        public readonly int $enum_type_stockage_ecs_id,
        public readonly int $enum_type_generateur_ecs_id,
        public readonly int $enum_usage_generateur_id,
        public readonly int $enum_type_energie_id,
        public readonly ?int $enum_periode_installation_ecs_thermo_id,

        public readonly ?int $tv_reseau_chaleur_id,
        public readonly ?int $tv_generateur_combustion_id,
        public readonly ?int $tv_pertes_stockage_id,
        public readonly ?int $tv_scop_id,

        public readonly ?float $pn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $rpn,
        public readonly ?float $cop,
        public readonly float $ratio_besoin_ecs,
        public readonly ?float $rendement_generation,
        public readonly ?float $rendement_generation_stockage,
        public readonly float $conso_ecs,
        public readonly float $conso_ecs_depensier,
        public readonly ?float $rendement_stockage
    ) {}

    /**
     * XPATH //installation_ecs_collection/installation_ecs/generateur_ecs_collection/generateur_ecs
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            reference_generateur_mixte: Normalizer::referenceval((string) $xml->donnee_entree->reference_generateur_mixte),
            description: (string) $xml->donnee_entree->description ?: null,
            ref_produit_generateur_ecs: (string) $xml->donnee_entree->ref_produit_generateur_ecs ?: null,
            identifiant_reseau_chaleur: (string) $xml->donnee_entree->identifiant_reseau_chaleur ?: null,
            date_arrete_reseau_chaleur: (string) $xml->donnee_entree->date_arrete_reseau_chaleur ?: null,
            position_volume_chauffe: (bool)(int) $xml->donnee_entree->position_volume_chauffe,
            position_volume_chauffe_stockage: (bool)(int) $xml->donnee_entree->position_volume_chauffe_stockage ?: null,
            volume_stockage: (float) $xml->donnee_entree->volume_stockage,
            presence_ventouse: (bool)(int) $xml->donnee_entree->presence_ventouse ?: null,

            enum_type_stockage_ecs_id: (int) $xml->donnee_entree->enum_type_stockage_ecs_id,
            enum_type_generateur_ecs_id: (int) $xml->donnee_entree->enum_type_generateur_ecs_id,
            enum_usage_generateur_id: (int) $xml->donnee_entree->enum_usage_generateur_id,
            enum_type_energie_id: (int) $xml->donnee_entree->enum_type_energie_id,
            enum_methode_saisie_carac_sys_id: (int) $xml->donnee_entree->enum_methode_saisie_carac_sys_id,
            enum_periode_installation_ecs_thermo_id: (int) $xml->donnee_entree->enum_periode_installation_ecs_thermo_id ?: null,

            tv_reseau_chaleur_id: (int) $xml->donnee_entree->tv_reseau_chaleur_id ?: null,
            tv_generateur_combustion_id: (int) $xml->donnee_entree->tv_generateur_combustion_id ?: null,
            tv_pertes_stockage_id: (int) $xml->donnee_entree->tv_pertes_stockage_id ?: null,
            tv_scop_id: (int) $xml->donnee_entree->tv_scop_id ?: null,

            pn: (float) $xml->donnee_intermediaire->pn ?: null,
            qp0: (float) $xml->donnee_intermediaire->qp0 ?: null,
            pveilleuse: (float) $xml->donnee_intermediaire->pveilleuse ?: null,
            rpn: (float) $xml->donnee_intermediaire->rpn ?: null,
            cop: (float) $xml->donnee_intermediaire->cop ?: null,
            ratio_besoin_ecs: (float) $xml->donnee_intermediaire->ratio_besoin_ecs,
            rendement_generation: (float) $xml->donnee_intermediaire->rendement_generation ?: null,
            rendement_generation_stockage: (float) $xml->donnee_intermediaire->rendement_generation_stockage ?: null,
            conso_ecs: (float) $xml->donnee_intermediaire->conso_ecs,
            conso_ecs_depensier: (float) $xml->donnee_intermediaire->conso_ecs_depensier,
            rendement_stockage: (float) $xml->donnee_intermediaire->rendement_stockage ?: null
        );
    }

    public function match(string $reference): bool
    {
        return $this->reference === $reference || str_contains($this->reference, $reference);
    }

    public function match_generateur_chauffage(GenerateurChauffage $generateur_chauffage): bool
    {
        return $generateur_chauffage->enum_type_generateur_ecs_id() === $this->enum_type_generateur_ecs_id
            && $generateur_chauffage->enum_type_energie_id === $this->enum_type_energie_id;
    }

    public function pn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->pn,
            default => null,
        };
    }

    public function rpn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->rpn,
            default => null,
        };
    }

    public function qp0_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->qp0,
            default => null,
        };
    }

    public function pveilleuse_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->pveilleuse,
            default => null,
        };
    }

    public function cop_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->cop,
            default => null,
        };
    }
}
