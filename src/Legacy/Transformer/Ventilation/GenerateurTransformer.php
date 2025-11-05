<?php

namespace App\Legacy\Transformer\Ventilation;

use App\Domain\Ventilation\Generateur\{TypeGenerateur, TypeVmc};
use App\Dto\Ventilation\GenerateurDto;
use App\Legacy\Model\Ventilation;
use App\Legacy\Transformer\Context;

final class GenerateurTransformer
{
    private Context $context;
    private Ventilation $ventilation;

    public function type_generateur(): ?TypeGenerateur
    {
        return match ($this->ventilation->enum_type_ventilation_id) {
            3, 4, 5, 6, 7, 8, 9, 13, 14, 15 => TypeGenerateur::VMC_SIMPLE_FLUX,
            10, 11, 12 => TypeGenerateur::VMC_SIMPLE_FLUX_GAZ,
            16, 17, 18 => TypeGenerateur::VMC_BASSE_PRESSION,
            19, 20, 21, 22, 23, 24 => TypeGenerateur::VMC_DOUBLE_FLUX,
            26, 27, 28, 29, 30, 31 => TypeGenerateur::VENTILATION_HYBRIDE,
            32, 33 => TypeGenerateur::VENTILATION_MECANIQUE,
            35, 36, 37, 38 => TypeGenerateur::PUIT_CLIMATIQUE,
            default => null,
        };
    }

    public function type_vmc(): ?TypeVmc
    {
        if (null === $this->type_generateur()) {
            return null;
        }
        if (false === $this->type_generateur()->is_vmc()) {
            return null;
        }
        return match ($this->ventilation->enum_type_ventilation_id) {
            3, 4, 5, 6, 16, 26, 27, 28 => TypeVmc::AUTOREGLABLE,
            7, 8, 9, 17 => TypeVmc::HYGROREGLABLE_TYPE_A,
            13, 14, 15, 18, 29, 30, 31 => TypeVmc::HYGROREGLABLE_TYPE_B,
            default => match ($this->ventilation->pvent_moy) {
                35, 65 => TypeVmc::AUTOREGLABLE,
                15, 50 => TypeVmc::HYGROREGLABLE_TYPE_A,
                80, 35 => TypeVmc::HYGROREGLABLE_TYPE_B,
                default => TypeVmc::AUTOREGLABLE,
            }
        };
    }

    public function annee_installation(): ?int
    {
        return match ($this->ventilation->enum_type_ventilation_id) {
            3 => 1981,
            4, 7, 10, 13, 26, 29 => 2000,
            5, 8, 11, 14, 19, 21, 23, 27, 30, 32, 35, 37 => 2012,
            6, 9, 12, 15, 20, 22, 24, 28, 31, 33, 36, 38 => $this->context->ressource()->administratif()->annee_etablissement(),
            default => null,
        };
    }

    public function presence_echangeur_thermique(): bool
    {
        return match ($this->ventilation->enum_type_ventilation_id) {
            19, 20, 21, 22, 37, 38 => true,
            23, 24, 35, 36 => false,
            default => false,
        };
    }

    public function generateur_collectif(): bool
    {
        return match ($this->ventilation->enum_type_ventilation_id) {
            21, 22 => true,
            default => false,
        };
    }

    public function __invoke(Ventilation $ventilation, Context $context): ?GenerateurDto
    {
        $this->context = $context;
        $this->ventilation = $ventilation;

        if (null === $this->type_generateur()) {
            return null;
        }
        return new GenerateurDto(
            id: $this->ventilation->id(),
            description: $this->ventilation->description(),
            type: $this->type_generateur(),
            presence_echangeur_thermique: $this->presence_echangeur_thermique(),
            generateur_collectif: $this->generateur_collectif(),
            annee_installation: $this->annee_installation(),
            type_vmc: $this->type_vmc(),
        );
    }
}
