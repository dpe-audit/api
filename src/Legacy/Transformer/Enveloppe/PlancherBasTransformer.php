<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Paroi\Inertie;
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
            u0: $paroi->upb0_saisi,
            u: $paroi->upb_saisi,
            isolation: new IsolationDto(
                etat: $this->etat_isolation(),
                type: $this->type_isolation(),
                annee_installation: $this->annee_isolation(),
                epaisseur: $this->epaisseur_isolation(),
                resistance_thermique: $paroi->resistance_isolation
            ),
            position: new PositionDto(
                surface: $paroi->surface(),
                mitoyennete: $this->mitoyennete(),
                surface_ue: $paroi->surface_ue,
                perimetre_ue: $paroi->perimetre_ue,
                local_non_chauffe_id: $this->local_non_chauffe_id()
            )
        );
    }
}
