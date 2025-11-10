<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\PlancherHaut\{Configuration, TypePlancherHaut};
use App\Dto\Enveloppe\Paroi\IsolationDto;
use App\Dto\Enveloppe\PlancherHaut\{PlancherHautDto, PositionDto};
use App\Legacy\Model\PlancherHaut;
use App\Legacy\Transformer\Context;

/**
 * @extends ParoiOpaqueTransformer<PlancherHaut>
 */
final class PlancherHautTransformer extends ParoiOpaqueTransformer
{
    public function configuration(): Configuration
    {
        return match ($this->paroi->enum_type_plancher_haut_id) {
            8, 11, 16 => Configuration::TERRASSE,
            12, 13 => Configuration::RAMPANTS,
            default => match ($this->paroi->enum_type_adjacence_id) {
                1, 2, 3, 5, 6 => Configuration::RAMPANTS,
                default => Configuration::PLANCHER,
            }
        };
    }

    public function type_structure(): ?TypePlancherHaut
    {
        return match ($this->paroi->enum_type_plancher_haut_id) {
            2 => TypePlancherHaut::PLAFOND_AVEC_OU_SANS_REMPLISSAGE,
            3 => TypePlancherHaut::PLAFOND_ENTRE_SOLIVES_METALLIQUES,
            4 => TypePlancherHaut::PLAFOND_ENTRE_SOLIVES_BOIS,
            5 => TypePlancherHaut::PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES,
            6 => TypePlancherHaut::PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES,
            7 => TypePlancherHaut::BARDEAUX_ET_REMPLISSAGE,
            8 => TypePlancherHaut::DALLE_BETON,
            9 => TypePlancherHaut::PLAFOND_BOIS_SUR_SOLIVES_BOIS,
            10 => TypePlancherHaut::PLAFOND_BOIS_SOUS_SOLIVES_BOIS,
            11 => TypePlancherHaut::PLAFOND_LOURD,
            12 => TypePlancherHaut::COMBLES_AMENAGES_SOUS_RAMPANT,
            13 => TypePlancherHaut::TOITURE_CHAUME,
            14 => TypePlancherHaut::PLAFOND_PATRE,
            15 => TypePlancherHaut::BAC_ACIER,
            default => null,
        };
    }

    public function u0(): ?float
    {
        return $this->paroi->uph0_saisi > 0 ? $this->paroi->uph0_saisi : null;
    }

    public function u(): ?float
    {
        return $this->paroi->uph_saisi > 0 ? $this->paroi->uph_saisi : null;
    }

    public function __invoke(PlancherHaut $plancher_haut, Context $context): PlancherHautDto
    {
        $this->context = $context;
        $this->paroi = $plancher_haut;

        return new PlancherHautDto(
            id: $plancher_haut->id(),
            description: $plancher_haut->description(),
            configuration: $this->configuration(),
            type_structure: $this->type_structure(),
            inertie: $this->inertie(),
            annee_construction: null,
            annee_renovation: null,
            u0: $this->u0(),
            u: $this->u(),
            isolation: new IsolationDto(
                etat: $this->etat_isolation(),
                type: $this->type_isolation(),
                annee_installation: $this->annee_isolation(),
                epaisseur: $this->epaisseur_isolation(),
                resistance_thermique: $this->resistance_isolation()
            ),
            position: new PositionDto(
                surface: $plancher_haut->surface(),
                mitoyennete: $this->mitoyennete(),
                orientation: null,
                local_non_chauffe_id: $this->local_non_chauffe_id()
            )
        );
    }
}
