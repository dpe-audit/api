<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Baie\Menuiserie\Materiau;
use App\Domain\Enveloppe\Baie\Position\TypePose;
use App\Domain\Enveloppe\Baie\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\Baie\{TypeBaie, TypeFermeture};
use App\Domain\Enveloppe\Baie\Vitrage\{NatureGazLame, TypeVitrage};
use App\Dto\Enveloppe\Baie\{BaieDto, MenuiserieDto, PositionDto, SurvitrageDto, VitrageDto};
use App\Legacy\Model\BaieVitree;
use App\Legacy\Transformer\Context;

/**
 * @extends ParoiTransformer<BaieVitree>
 */
final class BaieTransformer extends ParoiTransformer
{
    public function paroi_id(): ?string
    {
        if (null === $this->paroi->reference_paroi) {
            return null;
        }
        return $this->context->logement()->enveloppe->match_mur($this->paroi->reference_paroi)?->id()
            ?? $this->context->logement()->enveloppe->match_plancher_bas($this->paroi->reference_paroi)?->id()
            ?? $this->context->logement()->enveloppe->match_plancher_haut($this->paroi->reference_paroi)?->id();
    }

    public function double_fenetre_id(): ?string
    {
        return $this->paroi->baie_vitree_double_fenetre?->id();
    }

    public function type_baie(): TypeBaie
    {
        return match ($this->paroi->enum_type_baie_id) {
            1 => TypeBaie::BRIQUE_VERRE_PLEINE,
            2 => TypeBaie::BRIQUE_VERRE_CREUSE,
            3 => TypeBaie::POLYCARBONATE,
            4 => TypeBaie::FENETRE_BATTANTE,
            5 => TypeBaie::FENETRE_COULISSANTE,
            6 => TypeBaie::PORTE_FENETRE_COULISSANTE,
            7 => TypeBaie::PORTE_FENETRE_BATTANTE,
            8 => TypeBaie::PORTE_FENETRE_BATTANTE,
        };
    }

    public function type_fermeture(): TypeFermeture
    {
        return match ($this->paroi->enum_type_fermeture_id) {
            1 => TypeFermeture::SANS_FERMETURE,
            2 => TypeFermeture::VOLET_BATTANT_AVEC_AJOURS_FIXES,
            3 => TypeFermeture::FERMETURE_LAMES_ORIENTABLES,
            4 => TypeFermeture::VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_LTE_12MM,
            5 => TypeFermeture::VOLET_BATTANT_PVC_BOIS_EPAISSEUR_LTE_22MM,
            6 => TypeFermeture::VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_GT_12MM,
            7 => TypeFermeture::VOLET_BATTANT_PVC_BOIS_EPAISSEUR_GT_22MM,
            8 => TypeFermeture::FERMETURE_ISOLEE_SANS_AJOURS,
        };
    }

    public function presence_protection_solaire(): bool
    {
        return $this->paroi->presence_protection_solaire_hors_fermeture ?? false;
    }

    public function largeur_dormant(): int
    {
        return $this->paroi->largeur_dormant * 10;
    }

    public function type_pose(): ?TypePose
    {
        return match ($this->paroi->enum_type_pose_id) {
            1 => TypePose::NU_EXTERIEUR,
            2 => TypePose::NU_INTERIEUR,
            3 => TypePose::TUNNEL,
            4 => null,
        };
    }

    public function orientation(): ?float
    {
        return match ($this->paroi->enum_orientation_id) {
            1 => 180,
            2 => 0,
            3 => 90,
            4 => 270,
            default => null,
        };
    }

    public function inclinaison(): float
    {
        return match ($this->paroi->enum_inclinaison_vitrage_id) {
            1 => 15,
            2 => 50,
            3 => 90,
            4 => 0,
        };
    }

    public function presence_soubassement(): ?bool
    {
        return match ($this->paroi->enum_type_baie_id) {
            7 => true,
            8 => false,
            default => null,
        };
    }

    public function materiau(): ?Materiau
    {
        return match ($this->paroi->enum_type_materiaux_menuiserie_id) {
            3 => Materiau::BOIS,
            4 => Materiau::BOIS_METAL,
            5 => Materiau::PVC,
            6 => Materiau::METAL,
            7 => Materiau::METAL,
            default => null,
        };
    }

    public function type_vitrage(): TypeVitrage
    {
        if ($this->type_baie()->is_paroi_brique_verre()) {
            return TypeVitrage::BRIQUE_VERRE;
        }
        if ($this->type_baie()->is_paroi_polycarbonate()) {
            return TypeVitrage::POLYCARBONATE;
        }
        return match ($this->paroi->enum_type_vitrage_id) {
            1, 4 => TypeVitrage::SIMPLE_VITRAGE,
            2 => $this->paroi->vitrage_vir ? TypeVitrage::DOUBLE_VITRAGE_FE : TypeVitrage::DOUBLE_VITRAGE,
            3 => $this->paroi->vitrage_vir ? TypeVitrage::TRIPLE_VITRAGE_FE : TypeVitrage::TRIPLE_VITRAGE,
            5 => TypeVitrage::BRIQUE_VERRE,
            6 => TypeVitrage::POLYCARBONATE,
        };
    }

    public function type_survitrage(): ?TypeSurvitrage
    {
        return match ($this->paroi->enum_type_vitrage_id) {
            4 => $this->paroi->vitrage_vir ? TypeSurvitrage::SURVITRAGE_FE : TypeSurvitrage::SURVITRAGE_SIMPLE,
            1, 2, 3, 5, 6 => null,
        };
    }

    public function epaisseur_lame_survitrage(): ?float
    {
        if ($this->type_vitrage()->vitrage_complexe()) {
            return null;
        }
        if (null === $this->type_survitrage()) {
            return null;
        }
        return $this->paroi->epaisseur_lame;
    }

    public function nature_lame(): ?NatureGazLame
    {
        if (false === $this->type_vitrage()->vitrage_complexe()) {
            return null;
        }
        return match ($this->paroi->enum_type_gaz_lame_id) {
            1 => NatureGazLame::AIR,
            2 => NatureGazLame::ARGON,
            default => null,
        };
    }

    public function epaisseur_lame(): ?float
    {
        if (false === $this->type_vitrage()->vitrage_complexe()) {
            return null;
        }
        return $this->paroi->epaisseur_lame > 0 ? $this->paroi->epaisseur_lame : null;
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->paroi->enum_type_materiaux_menuiserie_id === 6 ? true : false;
    }

    public function ug(): ?float
    {
        return $this->paroi->ug_saisi > 0 ? $this->paroi->ug_saisi : null;
    }

    public function uw(): ?float
    {
        return $this->paroi->uw_saisi > 0 ? $this->paroi->uw_saisi : null;
    }

    public function ujn(): ?float
    {
        return $this->paroi->ujn_saisi > 0 ? $this->paroi->ujn_saisi : null;
    }

    public function sw(): ?float
    {
        return $this->paroi->sw_saisi > 0 ? $this->paroi->sw_saisi : null;
    }

    /**
     * @return array<string>
     */
    public function masques(): array
    {
        $collection = [];
        $collection[] = $this->paroi->masque_proche_id();
        $collection[] = $this->paroi->masque_lointain_id();

        foreach ($this->paroi->masque_lointain_non_homogene_collection as $masque) {
            $masques[] = $masque->id();
        }
        return array_filter($collection);
    }

    public function __invoke(BaieVitree $paroi, Context $context): ?BaieDto
    {
        $this->context = $context;
        $this->paroi = $paroi;

        if ($paroi->double_fenetre) {
            return null;
        }

        return new BaieDto(
            id: $paroi->id(),
            description: $paroi->description(),
            type: $this->type_baie(),
            presence_protection_solaire: $this->presence_protection_solaire(),
            type_fermeture: $this->type_fermeture(),
            annee_installation: null,
            ug: $this->ug(),
            uw: $this->uw(),
            ujn: $this->ujn(),
            sw: $this->sw(),
            position: new PositionDto(
                surface: $paroi->surface(),
                mitoyennete: $this->mitoyennete(),
                inclinaison: $this->inclinaison(),
                orientation: $this->orientation(),
                type_pose: $this->type_pose(),
                presence_soubassement: $this->presence_soubassement(),
                paroi_id: $this->paroi_id(),
                local_non_chauffe_id: $this->local_non_chauffe_id(),
                double_fenetre_id: $this->double_fenetre_id(),
            ),
            vitrage: new VitrageDto(
                type: $this->type_vitrage(),
                nature_lame: $this->nature_lame(),
                epaisseur_lame: $this->epaisseur_lame(),
            ),
            survitrage: $this->type_survitrage() ? new SurvitrageDto(
                type: $this->type_survitrage(),
                epaisseur_lame: $this->epaisseur_lame_survitrage(),
            ) : null,
            menuiserie: $this->type_baie()->is_paroi_vitree() ? null : new MenuiserieDto(
                materiau: $this->materiau(),
                largeur_dormant: $this->largeur_dormant(),
                presence_joint: $this->paroi->presence_joint,
                presence_retour_isolation: $this->paroi->presence_retour_isolation,
                presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique(),
            ),
            masques: $this->masques(),
        );
    }
}
