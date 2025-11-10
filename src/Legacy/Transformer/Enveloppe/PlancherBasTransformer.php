<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Domain\Enveloppe\PlancherBas\TypePlancherBas;
use App\Dto\Enveloppe\Paroi\IsolationDto;
use App\Dto\Enveloppe\PlancherBas\{PlancherBasDto, PositionDto};
use App\Legacy\Model\PlancherBas;
use App\Legacy\Transformer\Context;

/**
 * @extends ParoiOpaqueTransformer<PlancherBas>
 */
final class PlancherBasTransformer extends ParoiOpaqueTransformer
{
    public function type_structure(): ?TypePlancherBas
    {
        return match ($this->paroi->enum_type_plancher_bas_id) {
            2 => TypePlancherBas::PLANCHER_AVEC_OU_SANS_REMPLISSAGE,
            3 => TypePlancherBas::PLANCHER_ENTRE_SOLIVES_METALLIQUES,
            4 => TypePlancherBas::PLANCHER_ENTRE_SOLIVES_BOIS,
            5 => TypePlancherBas::PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES,
            6 => TypePlancherBas::BARDEAUX_ET_REMPLISSAGE,
            7 => TypePlancherBas::VOUTAINS_SUR_SOLIVES_METALLIQUES,
            8 => TypePlancherBas::VOUTAINS_BRIQUES_OU_MOELLONS,
            9 => TypePlancherBas::DALLE_BETON,
            10 => TypePlancherBas::PLANCHER_BOIS_SUR_SOLIVES_BOIS,
            11 => TypePlancherBas::PLANCHER_LOURD_TYPE_ENTREVOUS_TERRE_CUITE_OU_POUTRELLES_BETON,
            12 => TypePlancherBas::PLANCHER_ENTREVOUS_ISOLANT,
            default => null,
        };
    }

    public function surface_ue(): ?float
    {
        return match ($this->mitoyennete()) {
            Mitoyennete::ENTERRE,
            Mitoyennete::VIDE_SANITAIRE,
            Mitoyennete::TERRE_PLEIN,
            Mitoyennete::SOUS_SOL_NON_CHAUFFE => $this->paroi->surface_ue > 0 ? $this->paroi->surface_ue : $this->paroi->surface(),
            default => null,
        };
    }

    public function perimetre_ue(): ?float
    {
        return match ($this->mitoyennete()) {
            Mitoyennete::ENTERRE,
            Mitoyennete::VIDE_SANITAIRE,
            Mitoyennete::TERRE_PLEIN,
            Mitoyennete::SOUS_SOL_NON_CHAUFFE => $this->paroi->perimetre_ue > 0 ? $this->paroi->perimetre_ue : $this->surface_ue() / 4,
            default => null,
        };
    }

    public function u0(): ?float
    {
        return $this->paroi->upb0_saisi > 0 ? $this->paroi->upb0_saisi : null;
    }

    public function u(): ?float
    {
        return $this->paroi->upb_saisi > 0 ? $this->paroi->upb_saisi : null;
    }

    public function __invoke(PlancherBas $paroi, Context $context): PlancherBasDto
    {
        $this->context = $context;
        $this->paroi = $paroi;

        return new PlancherBasDto(
            id: $paroi->id(),
            description: $paroi->description(),
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
                surface: $paroi->surface(),
                mitoyennete: $this->mitoyennete(),
                surface_ue: $this->surface_ue(),
                perimetre_ue: $this->perimetre_ue(),
                local_non_chauffe_id: $this->local_non_chauffe_id()
            )
        );
    }
}
