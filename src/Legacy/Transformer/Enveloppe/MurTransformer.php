<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Mur\{TypeDoublage, TypeMur};
use App\Dto\Enveloppe\Mur\{MurDto, PositionDto};
use App\Dto\Enveloppe\Paroi\IsolationDto;
use App\Legacy\Model\Mur;
use App\Legacy\Transformer\Context;

/**
 * @extends ParoiOpaqueTransformer<Mur>
 */
final class MurTransformer extends ParoiOpaqueTransformer
{
    public function type_structure(): ?TypeMur
    {
        return match ($this->paroi->enum_materiaux_structure_mur_id) {
            2 => TypeMur::PIERRE_MOELLONS,
            3 => TypeMur::PIERRE_MOELLONS_AVEC_REMPLISSAGE,
            4 => TypeMur::PISE_OU_BETON_TERRE,
            5 => TypeMur::PAN_BOIS_SANS_REMPLISSAGE,
            6 => TypeMur::PAN_BOIS_AVEC_REMPLISSAGE,
            7 => TypeMur::BOIS_RONDIN,
            8 => TypeMur::BRIQUE_PLEINE_SIMPLE,
            9 => TypeMur::BRIQUE_PLEINE_DOUBLE_AVEC_LAME_AIR,
            10 => TypeMur::BRIQUE_CREUSE,
            11 => TypeMur::BLOC_BETON_PLEIN,
            12 => TypeMur::BLOC_BETON_CREUX,
            13 => TypeMur::BETON_BANCHE,
            14 => TypeMur::BETON_MACHEFER,
            15 => TypeMur::BRIQUE_TERRE_CUITE_ALVEOLAIRE,
            16 => TypeMur::BETON_CELLULAIRE,
            17 => TypeMur::BETON_CELLULAIRE,
            18 => TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT,
            19 => TypeMur::SANDWICH_BETON_ISOLANT_BETON_SANS_ISOLATION_RAPPORTEE,
            20 => TypeMur::CLOISON_PLATRE,
            24 => TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT,
            25 => TypeMur::OSSATURE_BOIS_SANS_REMPLISSAGE,
            26 => TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT,
            27 => TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_TOUT_VENANT,
            default => null,
        };
    }

    public function type_doublage(): ?TypeDoublage
    {
        return match ($this->paroi->enum_type_doublage_id) {
            2 => TypeDoublage::SANS_DOUBLAGE,
            3 => TypeDoublage::LAME_AIR_INFERIEUR_15MM,
            4 => TypeDoublage::LAME_AIR_SUPERIEUR_15MM,
            5 => TypeDoublage::MATERIAUX_CONNU,
            default => null,
        };
    }

    public function epaisseur_structure(): ?float
    {
        return $this->paroi->epaisseur_structure > 0 ? $this->paroi->epaisseur_structure : null;
    }

    public function u0(): ?float
    {
        return $this->paroi->umur0_saisi > 0 ? $this->paroi->umur0_saisi : null;
    }

    public function u(): ?float
    {
        return $this->paroi->umur_saisi > 0 ? $this->paroi->umur_saisi : null;
    }

    public function __invoke(Mur $paroi, Context $context): MurDto
    {
        $this->context = $context;
        $this->paroi = $paroi;

        return new MurDto(
            id: $paroi->id(),
            description: $paroi->description(),
            type_structure: $this->type_structure(),
            epaisseur_structure: $this->epaisseur_structure(),
            type_doublage: $this->type_doublage(),
            presence_enduit_isolant: $paroi->enduit_isolant_paroi_ancienne,
            paroi_ancienne: $paroi->enduit_isolant_paroi_ancienne,
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
                resistance_thermique: $this->resistance_isolation(),
            ),
            position: new PositionDto(
                surface: $paroi->surface(),
                mitoyennete: $this->mitoyennete(),
                orientation: null,
                local_non_chauffe_id: $this->local_non_chauffe_id()
            )
        );
    }
}
