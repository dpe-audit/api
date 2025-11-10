<?php

namespace App\Legacy\Transformer\Refroidissement;

use App\Domain\Refroidissement\Generateur\{TypeGenerateur, EnergieGenerateur};
use App\Dto\Refroidissement\GenerateurDto;
use App\Legacy\Model\Climatisation;
use App\Legacy\Transformer\Context;

final class GenerateurTransformer
{
    private Context $context;
    private Climatisation $climatisation;

    public function type_generateur(): TypeGenerateur
    {
        return match ($this->climatisation->enum_type_generateur_fr_id) {
            1, 2, 3 => TypeGenerateur::PAC_AIR_AIR,
            4, 5, 6, 7 => TypeGenerateur::PAC_AIR_EAU,
            8, 9, 10, 11 => TypeGenerateur::PAC_EAU_EAU,
            12, 13, 14, 15 => TypeGenerateur::PAC_EAU_GLYCOLEE_EAU,
            16, 17, 18, 19 => TypeGenerateur::PAC_GEOTHERMIQUE,
            20 => TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE,
            21 => TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE,
            22 => TypeGenerateur::AUTRE,
            23 => TypeGenerateur::RESEAU_FROID,
        };
    }

    public function energie_generateur(): EnergieGenerateur
    {
        if ($this->climatisation->enum_type_energie_id) {
            return match ($this->climatisation->enum_type_energie_id) {
                1, 12 => EnergieGenerateur::ELECTRICITE,
                2 => EnergieGenerateur::GAZ_NATUREL,
                9, 10, 13 => EnergieGenerateur::GPL,
                15 => EnergieGenerateur::RESEAU_FROID,
            };
        }
        return match ($this->climatisation->enum_type_generateur_fr_id) {
            21 => EnergieGenerateur::GAZ_NATUREL,
            23 => EnergieGenerateur::RESEAU_FROID,
            default => EnergieGenerateur::ELECTRICITE,
        };
    }

    public function annee_installation(): int
    {
        return match ($this->climatisation->enum_type_generateur_fr_id) {
            1, 4, 8, 12, 16 => 2007,
            2, 5, 9, 13, 17 => 2014,
            6, 10, 14, 18 => 2016,
            3, 7, 11, 15, 19 => $this->context->ressource()->administratif()->annee_etablissement(),
            20, 21, 22, 23 => match ($this->climatisation->enum_periode_installation_fr_id) {
                1 => 2007,
                2 => 2014,
                3 => $this->context->ressource()->administratif()->annee_etablissement(),
            }
        };
    }

    public function seer(): ?float
    {
        return $this->climatisation->seer_saisi() > 0 ? $this->climatisation->seer_saisi() : null;
    }

    public function __invoke(Climatisation $climatisation, Context $context): GenerateurDto
    {
        $this->context = $context;
        $this->climatisation = $climatisation;

        return new GenerateurDto(
            id: $climatisation->id(),
            reseau_froid_id: null,
            description: $climatisation->description(),
            type: $this->type_generateur(),
            energie: $this->energie_generateur(),
            annee_installation: $this->annee_installation(),
            seer: $this->seer(),
        );
    }
}
