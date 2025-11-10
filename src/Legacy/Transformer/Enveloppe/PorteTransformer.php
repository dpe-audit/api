<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Porte\{Isolation, Materiau};
use App\Domain\Enveloppe\Porte\Position\TypePose;
use App\Domain\Enveloppe\Porte\Vitrage\TypeVitrage;
use App\Dto\Enveloppe\Porte\MenuiserieDto;
use App\Dto\Enveloppe\Porte\{PorteDto, PositionDto, VitrageDto};
use App\Legacy\Model\Porte;
use App\Legacy\Transformer\Context;

/**
 * @extends ParoiTransformer<Porte>
 */
final class PorteTransformer extends ParoiTransformer
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

    public function isolation(): ?Isolation
    {
        return match ($this->paroi->enum_type_porte_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => Isolation::NON_ISOLE,
            13, 15 => Isolation::ISOLE,
            default => null,
        };
    }

    public function materiau(): ?Materiau
    {
        return match ($this->paroi->enum_type_porte_id) {
            1, 2, 3, 4 => Materiau::BOIS,
            5, 6, 7, 8 => Materiau::PVC,
            9, 10, 11, 12 => Materiau::METAL,
            default => null,
        };
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return match ($this->paroi->enum_type_porte_id) {
            2, 3, 6, 7, 10, 11 => TypeVitrage::SIMPLE_VITRAGE,
            4, 8, 12, 15 => TypeVitrage::DOUBLE_VITRAGE,
            1, 5, 9, 13, 14, 16 => null,
        };
    }

    public function surface_vitrage(): float
    {
        return match ($this->paroi->enum_type_porte_id) {
            2, 6, 11 => $this->paroi->surface() * 0.15,
            3, 7, 12 => $this->paroi->surface() * 0.45,
            4, 8, 10 => $this->paroi->surface() * 0.30,
            default => 0,
        };
    }

    public function presence_sas(): bool
    {
        return $this->paroi->enum_type_porte_id === 14;
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

    public function largeur_dormant(): ?int
    {
        return $this->paroi->largeur_dormant > 0 ? $this->paroi->largeur_dormant * 10 : null;
    }

    public function u(): ?float
    {
        return $this->paroi->uporte_saisi > 0 ? $this->paroi->uporte_saisi : null;
    }

    public function __invoke(Porte $porte, Context $context): PorteDto
    {
        $this->paroi = $porte;
        $this->context = $context;

        return new PorteDto(
            id: $porte->id(),
            description: $porte->description(),
            isolation: $this->isolation(),
            materiau: $this->materiau(),
            annee_installation: null,
            u: $this->u(),
            position: new PositionDto(
                type_pose: $this->type_pose(),
                surface: $porte->surface(),
                mitoyennete: $this->mitoyennete(),
                orientation: null,
                presence_sas: $this->presence_sas(),
                paroi_id: $this->paroi_id(),
                local_non_chauffe_id: $this->local_non_chauffe_id(),
            ),
            menuiserie: new MenuiserieDto(
                largeur_dormant: $this->largeur_dormant(),
                presence_joint: $porte->presence_joint,
                presence_retour_isolation: $porte->presence_retour_isolation,
            ),
            vitrage: new VitrageDto(
                surface: $this->surface_vitrage(),
                type: $this->type_vitrage(),
            )
        );
    }
}
